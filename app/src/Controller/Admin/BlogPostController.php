<?php

namespace App\Controller\Admin;

use App\DataMapper\BlogPostMapper;
use App\Entity\Post;
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

class BlogPostController
{
    private Twig $view;
    private EntityManager $entityManager;

    private Router $router;
    private ValidatorInterface $validator;
    private EntityRepository $blogPostRepository;

    public function __construct(Twig $view, EntityManager $entityManager, Router $router)
    {
        $this->view = $view;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->validator = Validation::createValidatorBuilder()
            ->addMethodMapping('loadValidatorMetadata')
            ->getValidator();
        $this->blogPostRepository = $this->entityManager->getRepository(Post::class);
    }
    public function list(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->view->render($response, 'admin/blog_post/list.twig', [
            'blogPosts' => $this->blogPostRepository->findAll()
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
            $blogPostMapper = new BlogPostMapper(new Post());
            $blogPost = $blogPostMapper->toBlogPost($request->getParsedBody());
            $violations = $this->validator->validate($blogPost);
            if ($violations->count() > 0) {
                $errorMessage = $violations[0]->getMessage();
                return $response->withRedirect(
                    $this->router->pathFor('admin_blog_posts_create')
                    .sprintf('?errorMessage=%s', urlencode($errorMessage))
                );
            }
            $this->entityManager->persist($blogPost);
            $this->entityManager->flush();

            return $response->withRedirect($this->router->pathFor('admin_blog_posts_list'));
        }

        return $this->view->render($response, 'admin/blog_post/create.twig', [
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
        $blogPost = $this->blogPostRepository->find($args['id']);
        if ($blogPost === null) {
            $response = new Response(404);
            return $response->write("Page not found");
        }

        $method = $request->getMethod();
        if ($method === 'POST') {
            $blogPostMapper = new BlogPostMapper($blogPost);
            $blogPost = $blogPostMapper->toBlogPost($request->getParsedBody());
            $violations = $this->validator->validate($blogPost);
            if ($violations->count() > 0) {
                $errorMessage = $violations[0]->getMessage();
                return $response->withRedirect(
                    $this->router->pathFor('admin_blog_posts_update')
                    .sprintf('?errorMessage=%s', urlencode($errorMessage))
                );
            }
            $this->entityManager->persist($blogPost);
            $this->entityManager->flush();

            return $response->withRedirect($this->router->pathFor('admin_blog_posts_list'));
        }
        return $this->view->render($response, 'admin/blog_post/update.twig', ['blogPost' => $blogPost]);
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
        $blogPost = $this->blogPostRepository->find($args['id']);
        if ($blogPost === null) {
            $response = new Response(404);
            return $response->write("Page not found");
        }

        $method = $request->getMethod();
        if ($method === 'POST') {
            $this->entityManager->remove($blogPost);
            $this->entityManager->flush();

            return $response->withRedirect($this->router->pathFor('admin_blog_posts_list'));
        }

        return $this->view->render($response, 'admin/blog_post/delete.twig', ['blogPost' => $blogPost]);
    }
}