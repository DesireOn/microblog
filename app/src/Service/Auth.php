<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManager;

class Auth
{
    private EntityManager $entityManager;
    private string $email;
    private string $password;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function login(string $email, string $password): void
    {
        $this->email = $email;
        $this->password = $password;

        if ($this->checkCredentials()) {
            $_SESSION['is_logged'] = true;
        }
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

    private function checkCredentials(): bool
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $this->email
        ]);
        if (!is_null($user)) {
            $hashedPassword = $user->getPassword();
            if (password_verify($this->password, $hashedPassword)) {
                return true;
            }
        }

        return false;
    }
}