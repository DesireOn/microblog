<?php

use App\Controller\Admin\UserController;
use App\Controller\LoginController;
use App\Middleware\AdminMiddleware;
use Slim\App;

return function (App $app) {
    $app->map(['GET', 'POST'],'/login', LoginController::class . ':login');
    $app->group('/admin', function (App $app) {
        $app->get('/users', UserController::class . ':list');
        $app->map(['GET', 'POST'], '/users/create', UserController::class . ':create');
    })->add(AdminMiddleware::class);
};