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
        $this->createModerator($manager);
    }

    private function createSuperAdmin($manager)
    {
        $user = new User();
        $user
            ->setEmail("admin@gamermind.com")
            ->setUsername("admin")
            ->setPassword('admin')
            ->setRoles([
                'ROLE_ADMIN',
                'ROLE_SUPER_ADMIN'
            ])
            ->setLocked(false);

        $manager->persist($user);
        $manager->flush();
    }

    private function createModerator($manager)
    {
        $user = new User();
        $user
            ->setEmail("moderator@gamermind.com")
            ->setUsername("moderator")
            ->setPassword('moderator')
            ->setRoles([
                'ROLE_USER',
                'ROLE_MODERATOR'
            ])
            ->setLocked(false);

        $manager->persist($user);
        $manager->flush();
    }
}
