<?php
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

$container = require_once __DIR__ . '/../app/bootstrap.php';

return ConsoleRunner::createHelperSet($container[EntityManager::class]);