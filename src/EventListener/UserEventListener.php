<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: User::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: User::class)]
class UserEventListener
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher){}

    public function prePersist(User $user, PrePersistEventArgs $event): void
    {
        $this->preWrite($user);
    }

    public function preUpdate(User $user, PreUpdateEventArgs $event): void
    {
        $this->preWrite($user);
    }

    private function preWrite(User $user): void
    {
        if (!empty($user->getPassword())) {
            $password = $this->passwordHasher
                ->hashPassword(
                    $user,
                    $user->getPassword()
                );
            $user->setPassword($password);
        }
    }
}
