<?php

use Slim\App;
use App\Middleware\CliMiddleware;

return function (App $app) {
    $container = $app->getContainer();
    $app->add(new CliMiddleware($container));
};