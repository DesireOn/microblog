<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;
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

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return Response|ResponseInterface
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function create(ServerRequestInterface $request, ResponseInterface $response): Response|ResponseInterface
    {
        $method = $request->getMethod();
        if ($method === 'POST') {
            if (isset($_POST['email'], $_POST['password'])) {
                $user = new User();
                $user->setEmail($_POST['email']);
                $user->setPassword(password_hash($_POST['password'], PASSWORD_DEFAULT));
                $user->setRoles(['ROLE_ADMIN']);

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                return $response->withRedirect('/admin/users/list', 301);
            }
            $errorMessage = 'Invalid Input';

            return $response->withRedirect(sprintf('/admin/users/create?errorMessage=%s', urlencode($errorMessage)));
        }

        return $this->view->render($response, 'admin/create.twig');
    }
}