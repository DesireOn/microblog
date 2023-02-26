<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManager;

class Auth
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function checkCredentials(string $email, string $password): bool
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $email
        ]);
        if (!is_null($user)) {
            $hashedPassword = $user->getPassword();
            if (password_verify($password, $hashedPassword)) {
                return true;
            }
        }

        return false;
    }

    public function isLogged(): bool
    {
        return empty($_SESSION['is_logged']) === false;
    }

    public function logout(): void
    {
        if ($this->isLogged() === true) {
            $_SESSION = [];
            session_unset();
            session_destroy();
        }
    }
}