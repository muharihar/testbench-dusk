<?php

use Orchestra\Testbench\ForkedServer;

// Simple server script, which pulls in large part from the framework.
// It has been adapted so we can reconstruct the application to the
// required state on each request (based on the calling Test Class)

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

// Laravel routes through here as std but we want to use the test setup
//require_once __DIR__.'/public/index.php';

define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';

// Identify the calling test class based on the content of the stash,
// use it to retrieve the built application as specificed in all
// the setup methods for the test class
$originatingTestClass = (new ForkedServer($_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT']))->getStash();
$app = (new $originatingTestClass)->getFreshApplication();

// Process the request as per a normal Laravel request
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$response->send();
$kernel->terminate($request, $response);