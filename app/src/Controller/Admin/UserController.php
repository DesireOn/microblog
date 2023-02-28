<?php

namespace App\Controller\Admin;

use App\DataMapper\UserMapper;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;
use Slim\Router;
use Slim\Views\Twig;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController
{
    private Twig $view;
    private EntityManager $entityManager;

    private Router $router;
    private ValidatorInterface $validator;
    private EntityRepository $userRepository;

    public function __construct(Twig $view, EntityManager $entityManager, Router $router)
    {
        $this->view = $view;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->validator = Validation::createValidatorBuilder()
            ->addMethodMapping('loadValidatorMetadata')
            ->getValidator();
        $this->userRepository = $this->entityManager->getRepository(User::class);
    }
    public function list(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->view->render($response, 'admin/user/list.twig', [
            'users' => $this->userRepository->findAll()
        ]);

        return $response;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = $request->getQueryParams();
        $method = $request->getMethod();
        if ($method === 'POST') {
            $userMapper = new UserMapper(new User());
            $user = $userMapper->toUser($_POST);
            $violations = $this->validator->validate($user);
            if ($violations->count() > 0) {
                $errorMessage = $violations[0]->getMessage();
                return $response->withRedirect(
                    $this->router->pathFor('admin_users_create')
                    .sprintf('?errorMessage=%s', urlencode($errorMessage))
                );
            }
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $response->withRedirect($this->router->pathFor('admin_users_list'));
        }

        return $this->view->render($response, 'admin/user/create.twig', [
            'errorMessage' => $params['errorMessage'] ?? null
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param $args
     * @return ResponseInterface
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function update(ServerRequestInterface $request, ResponseInterface $response, $args): ResponseInterface
    {
        $user = $this->userRepository->find($args['id']);
        if ($user === null) {
            $response = new Response(404);
            return $response->write("Page not found");
        }

        $method = $request->getMethod();
        if ($method === 'POST') {
            $userMapper = new UserMapper($user);
            $user = $userMapper->toUser($_POST);
            $violations = $this->validator->validate($user);
            if ($violations->count() > 0) {
                $errorMessage = $violations[0]->getMessage();
                return $response->withRedirect(
                    $this->router->pathFor('admin_users_list')
                    .sprintf('?errorMessage=%s', urlencode($errorMessage))
                );
            }
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $response->withRedirect($this->router->pathFor('admin_users_list'));
        }
        return $this->view->render($response, 'admin/user/update.twig', ['user' => $user]);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param $args
     * @return Response|ResponseInterface
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(ServerRequestInterface $request, ResponseInterface $response, $args): Response|ResponseInterface
    {
        $user = $this->userRepository->find($args['id']);
        if ($user === null) {
            $response = new Response(404);
            return $response->write("Page not found");
        }

        $method = $request->getMethod();
        if ($method === 'POST') {
            $this->entityManager->remove($user);
            $this->entityManager->flush();

            return $response->withRedirect($this->router->pathFor('admin_users_list'));
        }

        return $this->view->render($response, 'admin/user/delete.twig', ['user' => $user]);
    }
}