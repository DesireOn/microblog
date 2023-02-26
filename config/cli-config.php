<?php
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Slim\Container;

$container = new Container(require __DIR__ . '/../app/settings.php');

$bootstrap = require_once __DIR__ . '/../app/dependencies.php';
$bootstrap($container);

return ConsoleRunner::createHelperSet($container[EntityManager::class]);