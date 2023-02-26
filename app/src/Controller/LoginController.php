<?php

namespace App\Controller;

use App\Service\Auth;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class LoginController
{
    private Auth $auth;
    private Twig $view;

    public function __construct(Auth $auth, Twig $view)
    {
        $this->auth = $auth;
        $this->view = $view;
    }

    public function login(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->view->render($response, 'login.twig');
        return $response;
    }
}