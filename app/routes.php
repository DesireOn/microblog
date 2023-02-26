<?php

use App\Controller\Admin\LoginController;
use Slim\App;

return function (App $app) {
    $app->map(['GET', 'POST'],'/admin/login', LoginController::class . ':login');
};