<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Polyfill: request_parse_body() เป็น PHP 8.4 native function
// Symfony 7.2+ เรียกใช้สำหรับ PUT/PATCH/DELETE requests — ต้องกำหนดบน PHP 8.3
if (! function_exists('request_parse_body')) {
    function request_parse_body(): array
    {
        $ct = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($ct, 'application/x-www-form-urlencoded')) {
            parse_str(file_get_contents('php://input'), $post);

            return [$post, []];
        }

        return [$_POST, $_FILES];
    }
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
