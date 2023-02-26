<?php

namespace App\Controller\Admin;

use App\DataMapper\UserMapper;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController
{
    private Twig $view;
    private EntityManager $entityManager;
    private ValidatorInterface $validator;

    public function __construct(Twig $view, EntityManager $entityManager)
    {
        $this->view = $view;
        $this->entityManager = $entityManager;
        $this->validator = Validation::createValidatorBuilder()
            ->addMethodMapping('loadValidatorMetadata')
            ->getValidator();
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
     * @param array $args
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
                return $response->withRedirect(sprintf('/admin/users/create?errorMessage=%s', urlencode($errorMessage)));
            }
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $response->withRedirect('/admin/users/list');
        }

        return $this->view->render($response, 'admin/create.twig', [
            'errorMessage' => $params['errorMessage'] ?? null
        ]);
    }
}