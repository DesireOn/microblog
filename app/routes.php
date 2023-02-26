<?php

use App\Controller\LoginController;
use Slim\App;

return function (App $app) {
    $app->get('/login', LoginController::class . ':login');
};