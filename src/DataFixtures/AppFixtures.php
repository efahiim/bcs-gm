<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->createUsers($manager);
        $this->createProducts($manager);
    }

    private function createUsers(ObjectManager $manager) {
        $users = [
            ['admin@gamermind.com', 'admin', 'admin', ['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'], 0],
            ['moderator-mark@gamermind.com', 'mark', 'mark-mod', ['ROLE_USER', 'ROLE_MODERATOR'], 0],
            ['moderator-john@gamermind.com', 'john', 'john-mod', ['ROLE_USER', 'ROLE_MODERATOR'], 0],
            ['moderator-eric@gamermind.com', 'eric', 'eric-mod', ['ROLE_USER', 'ROLE_MODERATOR'], 0],
            ['charles@gmail.com', 'charles12', 'charles12', ['ROLE_USER'], 0],
            ['benjamin@gmail.com', 'benjamin24', 'benjamin24', ['ROLE_USER'], 0],
            ['carl@gmail.com', 'carl27', 'carl27', ['ROLE_USER'], 0],
            ['cody@live.com', 'cody49', 'cody49', ['ROLE_USER'], 0],
            ['jason@live.com', 'jason13', 'jason13', ['ROLE_USER'], 0],
            ['bruce@live.com', 'bruce78', 'bruce78', ['ROLE_USER'], 0],
            ['billy@hotmail.com', 'billy14', 'billy14', ['ROLE_USER'], 0],
            ['steven@hotmail.com', 'steven67', 'steven67', ['ROLE_USER'], 0],
            ['ryan@hotmail.com', 'ryan55', 'ryan55', ['ROLE_USER'], 0],
            ['craig@gmail.com', 'craig98', 'craig98', ['ROLE_USER'], 0],
            ['damon@gmail.com', 'damon33', 'damon33', ['ROLE_USER'], 0],
        ];

        foreach ($users as $user) {
            $newUser = new User();
            $newUser
                ->setEmail($user[0])
                ->setUsername($user[1])
                ->setPassword($user[2])
                ->setRoles($user[3])
                ->setLocked($user[4]);

            $manager->persist($newUser);
            $manager->flush();
        }
    }

    private function createProducts(ObjectManager $manager) {
        $products = [
            ['Elden Ring', 'Elden Ring is a 2022 action role-playing game developed by FromSoftware. It was directed by Hidetaka Miyazaki with worldbuilding provided by fantasy writer George R. R. Martin.', 59.99, 'Game', 50],
            ['God of War Ragnarök', 'God of War Ragnarök is an action-adventure game developed by Santa Monica Studio and published by Sony Interactive Entertainment.', 55.99, 'Game', 50],
            ['FIFA 2023', 'FIFA 23 is a football video game published by Electronic Arts. It is the 30th installment in the FIFA series that is developed by EA Sports, and the final installment under the FIFA banner.', 49.99, 'Game', 50],
            ['MGS V', 'Metal Gear Solid V: The Phantom Pain is a 2015 action-adventure stealth video game developed by Kojima Productions and published by Konami.', 39.99, 'Game', 50],
            ['Rachet & Clank', 'Ratchet & Clank is a series of action-adventure platform and third-person shooter video games created and developed by Insomniac Games and published by Sony Interactive Entertainment.', 49.99, 'Game', 50],
            ['Jak & Daxter', 'Jak and Daxter is an action-adventure video game franchise created by Andy Gavin and Jason Rubin and owned by Sony Interactive Entertainment.', 44.99, 'Game', 50],
            ['The Last of Us', 'The Last of Us is a 2020 action-adventure game developed by Naughty Dog and published by Sony Interactive Entertainment for the PlayStation 4.', 69.99, 'Game', 50],
            ['Call of Duty', 'Call of Duty is an American video game series and media franchise published by Activision.', 79.99, 'Game', 50],
            ['Overwatch 2', 'Overwatch is a multimedia franchise centered on a series of online multiplayer first-person shooter video games developed by Blizzard Entertainment.', 29.99, 'Game', 50],
            ['World of Warcraft', 'World of Warcraft is a massively multiplayer online role-playing game released in 2004 by Blizzard Entertainment.', 79.99, 'Game', 50],
            ['League of Legends', 'League of Legends, commonly referred to as League, is a 2009 multiplayer online battle arena video game developed and published by Riot Games.', 34.99, 'Game', 50],
            ['Fortnite', 'Fortnite is an online video game developed by Epic Games and released in 2017.', 54.99, 'Game', 50],
            ['Rocket League', 'Rocket League is a vehicular soccer video game developed and published by Psyonix.', 29.99, 'Game', 50],
            ['Apex Legends', 'Apex Legends is a free-to-play battle royale-hero shooter game developed by Respawn Entertainment and published by Electronic Arts.', 44.99, 'Game', 50],
            ['CS: GO', 'Counter-Strike: Global Offensive is a 2012 multiplayer tactical first-person shooter developed by Valve and Hidden Path Entertainment.', 39.99, 'Game', 50],
            ['PUBG', 'PUBG: Battlegrounds is a battle royale game developed by PUBG Studios and published by Krafton.', 37.99, 'Game', 50],
            ['Minecraft', 'Minecraft is a 2011 sandbox game developed by Mojang Studios.', 24.99, 'Game', 50],
            ['Grand Theft Auto V', 'Grand Theft Auto V is a 2013 action-adventure game developed by Rockstar North and published by Rockstar Games.', 64.99, 'Game', 50],
            ['Forza Horizon 5', 'Forza Horizon 5 is a 2021 racing video game developed by Playground Games and published by Xbox Game Studios.', 54.99, 'Game', 50],
            ['Street Fighter 6', 'Street Fighter 6 is a 2023 fighting game developed and published by Capcom.', 34.99, 'Game', 50],
            ['XBOX Series X', 'The fastest, most powerful XBOX ever.', 399.99, 'Device', 50],
            ['XBOX Series S', 'Simply next-gen.', 259.99, 'Device', 50],
            ['Playstation 5', 'Experience lightning-fast loading with an ultra-high speed SSD, deeper immersion with support for haptic feedback, adaptive triggers and 3D Audio*, and an all-new generation of incredible PlayStation games.', 349.99, 'Device', 50],
            ['Nintendo Switch', 'The Nintendo Switch is a hybrid video game console developed by Nintendo and released worldwide in most regions on March 3, 2017.', 249.99, 'Device', 50],
            ['Steam Deck', 'The Steam Deck is a handheld gaming computer developed by Valve and released on February 25, 2022.', 279.99, 'Device', 50],
        ];

        foreach ($products as $product) {
            $newProduct = new Product();
            $newProduct
                ->setTitle($product[0])
                ->setDescription($product[1])
                ->setPrice($product[2])
                ->setType($product[3])
                ->setStock($product[4]);

            $manager->persist($newProduct);
            $manager->flush();
        }
    }
}
