<?php

use Slim\App;

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../app/settings.php';
$app = new App($settings);

// Register dependencies
$dependencies = require __DIR__ . '/../app/dependencies.php';
$dependencies($app->getContainer());

// Register middleware
$middleware = require __DIR__ . '/../app/middleware.php';
$middleware($app);

try {
    $app->run();
} catch (Throwable $e) {
    echo $e->getMessage();
}