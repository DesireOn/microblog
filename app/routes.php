<?php

use App\Controller\LoginController;
use Slim\App;

return function (App $app) {
    $app->map(['GET', 'POST'],'/login', LoginController::class . ':login');
};