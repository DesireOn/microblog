<?php

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;

class UserDataLoader implements FixtureInterface
{
    private const USERS = [
        [
            'email' => 'test0@gmail.com',
            'password' => '123',
            'roles' => ['ROLE_ADMIN']
        ],
        [
            'email' => 'test1@gmail.com',
            'password' => '123',
            'roles' => ['ROLE_ADMIN']
        ],
        [
            'email' => 'test2@gmail.com',
            'password' => '123',
            'roles' => ['ROLE_ADMIN']
        ],
        [
            'email' => 'test3@gmail.com',
            'password' => '123',
            'roles' => ['ROLE_ADMIN']
        ],
        [
            'email' => 'test4@gmail.com',
            'password' => '123',
            'roles' => ['ROLE_ADMIN']
        ],
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::USERS as $currentUser) {
            $newUser = new User();

            $newUser->setEmail($currentUser['email']);
            $newUser->setPassword(password_hash($currentUser['password'], PASSWORD_DEFAULT));
            $newUser->setRoles($currentUser['roles']);

            $manager->persist($newUser);
        }
        $manager->flush();
    }
}