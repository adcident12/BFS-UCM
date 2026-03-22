<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MinifyHtml
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->isHtmlResponse($response)) {
            return $response;
        }

        $content = $response->getContent();

        if ($content === false || strlen($content) < 512) {
            return $response;
        }

        $response->setContent($this->minify($content));

        return $response;
    }

    private function isHtmlResponse(Response $response): bool
    {
        $type = $response->headers->get('Content-Type', '');

        return str_contains($type, 'text/html');
    }

    private function minify(string $html): string
    {
        // ── Step 1: Preserve blocks that must not be touched ──────────────
        // <pre>, <script>, <style>, <textarea> — whitespace is significant
        $preserved = [];
        $token = "\x02UCM_PRESERVE_%d\x03";

        $blocks = ['/<pre[\s>][\s\S]*?<\/pre>/i', '/<script[\s>][\s\S]*?<\/script>/i', '/<style[\s>][\s\S]*?<\/style>/i', '/<textarea[\s>][\s\S]*?<\/textarea>/i'];

        foreach ($blocks as $pattern) {
            $html = preg_replace_callback($pattern, function (array $m) use (&$preserved, $token): string {
                $key = count($preserved);
                $preserved[$key] = $m[0];

                return sprintf($token, $key);
            }, $html) ?? $html;
        }

        // ── Step 2: Remove HTML comments (keep IE conditional comments) ───
        $html = preg_replace('/<!--(?!\[if\s)[\s\S]*?-->/i', '', $html) ?? $html;

        // ── Step 3: Collapse whitespace ───────────────────────────────────
        // Remove whitespace between HTML tags (safe for Tailwind CSS apps)
        $html = preg_replace('/>\s+</s', '><', $html) ?? $html;
        // Collapse runs of 2+ spaces / tabs / newlines within text content
        $html = preg_replace('/\s{2,}/', ' ', $html) ?? $html;

        // ── Step 4: Restore preserved blocks ─────────────────────────────
        foreach ($preserved as $key => $block) {
            $html = str_replace(sprintf($token, $key), $block, $html);
        }

        return trim($html);
    }
}
