<?php

use App\Command\LoadFixtures;
use App\Controller\Admin\LoginController;
use App\Middleware\AdminMiddleware;
use App\Service\Auth;
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
            $c->get(EntityManager::class)
        );
    };

    // Load LoginController
    $container[AdminMiddleware::class] = function (Container $c): AdminMiddleware {
        return new AdminMiddleware($c->get(Auth::class));
    };

    return $container;
};