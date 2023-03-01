<?php

use App\Command\LoadFixtures;
use App\Controller\Admin\BlogPostController;
use App\Controller\Admin\BlogPostImageController;
use App\Controller\Admin\UserController;
use App\Controller\LoginController;
use App\Middleware\AdminMiddleware;
use App\Service\Auth;
use App\Service\Uploader;
use Doctrine\Common\Cache\Psr6\DoctrineProvider;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Slim\Container;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

return function (Container $container) {
    // Load EntityManager
    $container[EntityManager::class] = function (Container $c): EntityManager {
        /** @var array $settings */
        $settings = $c->get('settings');

        // Use the ArrayAdapter or the FilesystemAdapter depending on the value of the 'dev_mode' setting
        // You can substitute the FilesystemAdapter for any other cache you prefer from the symfony/cache library
        $cache = $settings['doctrine']['dev_mode'] ?
            DoctrineProvider::wrap(new ArrayAdapter()) :
            DoctrineProvider::wrap(new FilesystemAdapter(directory: $settings['doctrine']['cache_dir']));

        $config = Setup::createAttributeMetadataConfiguration(
            $settings['doctrine']['metadata_dirs'],
            $settings['doctrine']['dev_mode'],
            null,
            $cache
        );

        return EntityManager::create($settings['doctrine']['connection'], $config);
    };

    // Load Fixtures
    $container[LoadFixtures::class] = function (Container $c): LoadFixtures {
        return new LoadFixtures($c->get(EntityManager::class));
    };

    // Load Auth
    $container[Auth::class] = function (Container $c): Auth {
        return new Auth($c->get(EntityManager::class));
    };

    // Load Twig
    $container['view'] = function ($c) {
        $settings = $c->get('settings');
        $view = new Slim\Views\Twig($settings['view']['template_path'], $settings['view']['twig']);

        // Add extensions
        $view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $c->get('request')->getUri()));

        return $view;
    };

    // Load LoginController
    $container[LoginController::class] = function (Container $c): LoginController {
        return new LoginController(
            $c->get(Auth::class),
            $c->get('view'),
            $c->get('router')
        );
    };

    // Load AdminMiddleware
    $container[AdminMiddleware::class] = function (Container $c): AdminMiddleware {
        return new AdminMiddleware($c->get(Auth::class));
    };

    // Load Admin\UserController
    $container[UserController::class] = function (Container $c): UserController {
        return new UserController(
            $c->get('view'),
            $c->get(EntityManager::class),
            $c->get('router')
        );
    };

    // Load Admin\BlogPostController
    $container[BlogPostController::class] = function (Container $c): BlogPostController {
        return new BlogPostController(
            $c->get('view'),
            $c->get(EntityManager::class),
            $c->get('router')
        );
    };

    // Load Uploader
    $container[Uploader::class] = function (Container $c): Uploader {
        return new Uploader();
    };

    // Load Admin\BlogPostController
    $container[BlogPostImageController::class] = function (Container $c): BlogPostImageController {
        return new BlogPostImageController(
            $c->get('view'),
            $c->get(EntityManager::class),
            $c->get('router'),
            $c->get('settings')['uploadDirectory'],
            $c->get(Uploader::class)
        );
    };

    return $container;
};