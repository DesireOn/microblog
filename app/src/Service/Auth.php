<?php

namespace App\Service;

use Doctrine\ORM\EntityManager;

class Auth
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
}