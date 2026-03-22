<?php
echo json_encode([
    'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? 'MISSING',
    'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? 'MISSING',
    'SCRIPT_NAME' => $_SERVER['SCRIPT_NAME'] ?? 'MISSING',
    'SCRIPT_FILENAME' => $_SERVER['SCRIPT_FILENAME'] ?? 'MISSING',
    'PATH_INFO' => $_SERVER['PATH_INFO'] ?? 'none',
    'PHP_SELF' => $_SERVER['PHP_SELF'] ?? 'none',
]);
