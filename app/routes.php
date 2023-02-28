<?php

use App\Controller\Admin\BlogPostController;
use App\Controller\Admin\UserController;
use App\Controller\LoginController;
use App\Middleware\AdminMiddleware;
use Slim\App;

return function (App $app) {
    $app->map(['GET', 'POST'],'/login', LoginController::class . ':login')
        ->setName('login');
    $app->group('/admin', function (App $app) {
        // Users routes
        $app->get('/users/list', UserController::class . ':list')
            ->setName('admin_users_list');
        $app->map(['GET', 'POST'], '/users/create', UserController::class . ':create')
            ->setName('admin_users_create');
        $app->map(['GET', 'POST'], '/users/update/{id}', UserController::class . ':update')
            ->setName('admin_users_update');
        // Blog posts routes
        $app->get('/blog-posts/list', BlogPostController::class . ':list')
            ->setName('admin_blog_posts_list');
        $app->map(['GET', 'POST'], '/blog-posts/create', BlogPostController::class . ':create')
            ->setName('admin_blog_posts_create');
        $app->map(['GET', 'POST'], '/blog-posts/update/{id}', BlogPostController::class . ':update')
            ->setName('admin_blog_posts_update');
        $app->map(['GET', 'POST'], '/blog-posts/delete/{id}', BlogPostController::class . ':delete')
            ->setName('admin_blog_posts_delete');
    })->add(AdminMiddleware::class);
};