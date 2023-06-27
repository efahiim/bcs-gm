<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->createSuperAdmin($manager);
    }

    private function createSuperAdmin($manager)
    {
        $user = new User();
        $user
            ->setEmail("admin@gamermind.com")
            ->setUsername("gmadmin")
            ->setPassword('gmadmin')
            ->setRoles([
                'ROLE_ADMIN',
                'ROLE_SUPER_ADMIN'
            ])
            ->setLocked(false);

        $manager->persist($user);
        $manager->flush();
    }
}
