<?php

use Slim\App;

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../app/settings.php';
$app = new App($settings);

try {
    $app->run();
} catch (Throwable $e) {
    echo $e->getMessage();
}