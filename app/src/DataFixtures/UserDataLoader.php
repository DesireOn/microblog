<?php

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;

class UserDataLoader implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('desireon98@gmail.com');
        $user->setPassword(password_hash('12345', PASSWORD_DEFAULT));
        $user->setRoles(['ROLE_ADMIN']);

        $manager->persist($user);
        $manager->flush();
    }
}