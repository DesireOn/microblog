<?php

use App\Controller\Admin\LoginController;
use App\Middleware\AdminMiddleware;
use Slim\App;

return function (App $app) {
    $app->group('/admin', function (App $app) {
        $app->map(['GET', 'POST'],'/login', LoginController::class . ':login');
    })->add(AdminMiddleware::class);
};