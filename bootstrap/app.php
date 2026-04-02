<?php

use App\Http\Middleware\CheckOAuthScope;
use App\Http\Middleware\MinifyHtml;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(
            at: ['127.0.0.1', '10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16'],
            headers: Request::HEADER_X_FORWARDED_FOR |
                     Request::HEADER_X_FORWARDED_HOST |
                     Request::HEADER_X_FORWARDED_PORT |
                     Request::HEADER_X_FORWARDED_PROTO,
        );

        $middleware->web(append: [
            MinifyHtml::class,
        ]);

        $middleware->alias([
            'oauth.scope' => CheckOAuthScope::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Render HTTP exceptions with custom branded pages
        $exceptions->render(function (HttpExceptionInterface $e, Request $request): ?Response {
            if ($request->expectsJson()) {
                return null; // ปล่อยให้ Laravel จัดการ JSON response เอง
            }

            $status = $e->getStatusCode();
            $view = "errors.{$status}";

            if (view()->exists($view)) {
                return response()->view($view, ['exception' => $e], $status);
            }

            // Fallback สำหรับ 5xx ที่ไม่มี custom page
            if ($status >= 500 && view()->exists('errors.500')) {
                return response()->view('errors.500', ['exception' => $e], $status);
            }

            return null;
        });

        // Log server errors ด้วย context ที่ครบ
        $exceptions->report(function (Throwable $e): void {
            if (! ($e instanceof HttpExceptionInterface)) {
                Log::error('[UCM] Unhandled exception: '.$e->getMessage(), [
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'url' => request()->fullUrl(),
                    'user' => auth()->id(),
                ]);
            }
        })->stop(); // ป้องกัน double-logging

    })->create();
