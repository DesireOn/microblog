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

    public function __construct(
        Auth $auth,
        Twig $view,
    )
    {
        $this->auth = $auth;
        $this->view = $view;
    }

    public function login(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if ($this->auth->isLogged() === true) {
            return $response->withRedirect('/admin');
        }

        if ($request->getMethod() === 'POST' && isset($_POST['email'], $_POST['password'])) {
            if ($this->auth->checkCredentials($_POST['email'], $_POST['password'])) {
                $_SESSION['is_logged'] = true;
                return $response->withRedirect('/admin', 301);
            } else {
                $errorMessage = 'Invalid Credentials';
                return $response->withRedirect(sprintf('/login?errorMessage=%s', urlencode($errorMessage)));
            }
        }

        $params = $request->getQueryParams();
        $errorMessage = $params['errorMessage'] ?? null;
        $this->view->render($response, 'login.twig', ['errorMessage' => $errorMessage]);

        return $response;
    }
}