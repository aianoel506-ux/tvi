<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        http_response_code(200);
        header('Content-Type: text/plain');
        echo "=== TVI PHP ERROR ON VERCEL ===\n";
        echo "Type: " . $error['type'] . "\n";
        echo "Message: " . $error['message'] . "\n";
        echo "File: " . $error['file'] . "\n";
        echo "Line: " . $error['line'] . "\n";
    }
});

set_exception_handler(function($e) {
    http_response_code(200);
    header('Content-Type: text/plain');
    echo "=== TVI UNCAUGHT EXCEPTION ===\n";
    echo $e->getMessage() . "\n" . $e->getTraceAsString();
});

// Set script environment variables for CodeIgniter router
$_SERVER['SCRIPT_FILENAME'] = dirname(__DIR__) . '/index.php';
$_SERVER['SCRIPT_NAME']     = '/index.php';

// Change current working directory to the project root
chdir(dirname(__DIR__));

// Bootstrap CodeIgniter
require dirname(__DIR__) . '/index.php';
