<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class UserController
{
    private Twig $view;
    private EntityManager $entityManager;

    public function __construct(Twig $view, EntityManager $entityManager)
    {
        $this->view = $view;
        $this->entityManager = $entityManager;
    }
    public function list(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $repository = $this->entityManager->getRepository(User::class);
        $users = $repository->findAll();

        $this->view->render($response, 'admin/list.twig', ['users' => $users]);

        return $response;
    }
}