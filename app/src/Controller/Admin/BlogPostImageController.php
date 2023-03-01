<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Service\Uploader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;
use Slim\Router;
use Slim\Views\Twig;

class BlogPostImageController
{
    private Twig $view;
    private EntityManager $entityManager;
    private Router $router;
    private string $uploadDirectory;
    private EntityRepository $blogPostRepository;
    private Uploader $uploader;

    /**
     * @param Twig $view
     * @param EntityManager $entityManager
     * @param Router $router
     * @param string $uploadDirectory
     * @param Uploader $uploader
     */
    public function __construct(
        Twig $view,
        EntityManager $entityManager,
        Router $router,
        string $uploadDirectory,
        Uploader $uploader
    )
    {
        $this->view = $view;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->uploadDirectory = $uploadDirectory;
        $this->blogPostRepository = $this->entityManager->getRepository(Post::class);
        $this->uploader = $uploader;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param $args
     * @return ResponseInterface
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    public function upload(ServerRequestInterface $request, ResponseInterface $response, $args): ResponseInterface
    {
        /** @var Post $blogPost */
        $blogPost = $this->blogPostRepository->find($args['id']); // TODO: move to a middleware
        if ($blogPost === null) {
            $response = new Response(404);
            return $response->write("Page not found");
        }
        if ($this->checkIfThereIsImage($blogPost)) {
            return $response->withRedirect($this->router->pathFor('admin_blog_posts_update', ['id' => $args['id']]));
        }

        $method = $request->getMethod();
        if ($method === 'POST') {
            $uploadedDirectory = $this->uploadDirectory;

            $uploadedFiles = $request->getUploadedFiles();

            $uploadedFile = $uploadedFiles['featuredImage'];
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                $fileName = $this->uploader->upload($uploadedDirectory, $uploadedFile);
                $blogPost->setFeaturedImage($fileName);
                $this->entityManager->persist($blogPost);
                $this->entityManager->flush();
            }
            return $response->withRedirect($this->router->pathFor('admin_blog_posts_update', ['id' => $args['id']]));
        }
        return $this->view->render($response, 'admin/blog_post_image/upload.twig', ['blogPost' => $blogPost]);
    }

    private function checkIfThereIsImage(Post $blogPost): bool
    {
        if ($blogPost->getFeaturedImage() !== null) {
            return true;
        }

        return false;
    }
}