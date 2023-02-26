<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Service\Auth;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class LoginController
{
    private Auth $auth;
    private Twig $view;
    private EntityManager $entityManager;

    public function __construct(
        Auth $auth,
        Twig $view,
        EntityManager $entityManager
    )
    {
        $this->auth = $auth;
        $this->view = $view;
        $this->entityManager = $entityManager;
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
                return $response->withRedirect(sprintf('/admin/login?errorMessage=%s', urlencode($errorMessage)));
            }
        }

        $params = $request->getQueryParams();
        $errorMessage = $params['errorMessage'] ?? null;
        $this->view->render($response, 'admin/login.twig', ['errorMessage' => $errorMessage]);

        return $response;
    }

    public function list(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $repository = $this->entityManager->getRepository(User::class);
        $users = $repository->findAll();

        $this->view->render($response, 'list.twig', ['users' => $users]);

        return $response;
    }
}