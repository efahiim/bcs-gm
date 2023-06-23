<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Review;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->createSuperAdmin($manager);

        $review1 = new Review();
        $review1->setComment('This is the first comment for Game One');
        $review1->setRating(4);
        $manager->persist($review1);

        $review2 = new Review();
        $review2->setComment('This is the second comment for Game One');
        $review2->setRating(3);
        $manager->persist($review2);

        $this->addReference('review_1', $review1);
        $this->addReference('review_2', $review2);

        $product1 = new Product();
        $product1->setTitle('Game One');
        $product1->setDescription('This is the description of Game One');
        $product1->setImage('/assets/images/game1.jpg');
        $product1->setPrice(49.99);
        $product1->setType('game');
        $product1->addReview($this->getReference('review_1'));
        $product1->addReview($this->getReference('review_2'));
        $manager->persist($product1);

        $product2 = new Product();
        $product2->setTitle('Device One');
        $product2->setDescription('This is the description of Game One');
        $product2->setImage('/assets/images/game2.jpg');
        $product2->setPrice(49.99);
        $product2->setType('device');
        $manager->persist($product2);

        $product3 = new Product();
        $product3->setTitle('Device One');
        $product3->setDescription('This is the description of Game One');
        $product3->setImage('/assets/images/game3.jpg');
        $product3->setPrice(49.99);
        $product3->setType('device');
        $manager->persist($product3);
        
        $manager->flush();
    }

    private function createSuperAdmin($manager)
    {
        $user = new User();
        $user
            ->setEmail("admin@gamermind.com")
            ->setPassword('gmadmin')
            ->setRoles([
                'ROLE_SUPER_ADMIN'
            ]);

        $manager->persist($user);
        $manager->flush();
    }
}
