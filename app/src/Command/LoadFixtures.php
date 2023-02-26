<?php

namespace App\Command;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LoadFixtures implements CommandInterface
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $loader = new Loader();
        $loader->loadFromDirectory(__DIR__ . '/../DataFixtures');

        $executor = new ORMExecutor($this->entityManager, new ORMPurger());
        $executor->execute($loader->getFixtures());

        return $response;
    }
}