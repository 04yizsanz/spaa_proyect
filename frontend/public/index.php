<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$projectRoot = dirname(__DIR__, 2);

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $projectRoot.'/backend/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $projectRoot.'/backend/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $projectRoot.'/backend/bootstrap/app.php';

$app->handleRequest(Request::capture());
