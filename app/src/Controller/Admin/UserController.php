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
        $this->view->render($response, 'admin/list.twig', [
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

        return $this->view->render($response, 'admin/create.twig', [
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
        $repository = $this->entityManager->getRepository(User::class);
        $user = $repository->find($args['id']);
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
                    sprintf(
                        '/admin/users/update?errorMessage=%s',
                        urlencode($errorMessage)
                    )
                );
            }
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $response->withRedirect('/admin/users/list');
        }
        return $this->view->render($response, 'admin/update.twig', ['user' => $user]);
    }
}