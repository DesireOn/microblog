<?php

use App\Controller\LoginController;
use App\Middleware\AdminMiddleware;
use Slim\App;

return function (App $app) {
    $app->map(['GET', 'POST'],'/login', LoginController::class . ':login');
    $app->group('/admin', function (App $app) {
        $app->get('/users', LoginController::class . ':list');
    })->add(AdminMiddleware::class);
};