<?php
// Override autoload and just check what REQUEST_URI and method reach Laravel
define('LARAVEL_START', microtime(true));
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/index.php';

require_once __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$request = Illuminate\Http\Request::capture();
echo json_encode([
    'method' => $request->method(),
    'path' => $request->path(),
    'uri' => $request->getRequestUri(),
    'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'MISSING',
    'request_uri' => $_SERVER['REQUEST_URI'] ?? 'MISSING',
    'real_method' => $_SERVER['REQUEST_METHOD'] ?? 'MISSING',
]);
