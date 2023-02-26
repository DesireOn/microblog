<?php

use App\Controller\Admin\UserController;
use App\Controller\LoginController;
use App\Middleware\AdminMiddleware;
use Slim\App;

return function (App $app) {
    $app->map(['GET', 'POST'],'/login', LoginController::class . ':login')
        ->setName('login');
    $app->group('/admin', function (App $app) {
        $app->get('/users/list', UserController::class . ':list')
            ->setName('admin_users_list');
        $app->map(['GET', 'POST'], '/users/create', UserController::class . ':create')
            ->setName('admin_users_create');
        $app->map(['GET', 'POST'], '/users/update/{id}', UserController::class . ':update')
            ->setName('admin_users_update');
    })->add(AdminMiddleware::class);
};