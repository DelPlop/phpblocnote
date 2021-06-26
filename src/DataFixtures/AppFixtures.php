<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Note;
use App\Entity\NoteCategory;
use App\Entity\RegisteredUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        $visibility = [
            'private',
            'shared',
            'public'
        ];

        // users
        $user = new RegisteredUser();
        $user->setLogin('Test1')
            ->setEmail('test1@mail.fr')
            ->setRoles(['ROLE_USER'])
            ->setPassword('$argon2id$v=19$m=65536,t=4,p=1$DreOeq5kCOYMTpd8AtzkpQ$xyAdrrC2DAWwgLockGggUa3zImNwG/faGV6WXj2SkZY');   // test
        $manager->persist($user);

        $user2 = new RegisteredUser();
        $user2->setLogin('Test2')
            ->setEmail('test2@mail.fr')
            ->setRoles(['ROLE_USER'])
            ->setPassword('$argon2id$v=19$m=65536,t=4,p=1$DreOeq5kCOYMTpd8AtzkpQ$xyAdrrC2DAWwgLockGggUa3zImNwG/faGV6WXj2SkZY');   // test
        $manager->persist($user2);

        $user3 = new RegisteredUser();
        $user3->setLogin('Test3')
            ->setEmail('test3@mail.fr')
            ->setRoles(['ROLE_USER'])
            ->setPassword('$argon2id$v=19$m=65536,t=4,p=1$DreOeq5kCOYMTpd8AtzkpQ$xyAdrrC2DAWwgLockGggUa3zImNwG/faGV6WXj2SkZY');   // test
        $manager->persist($user3);

        $users = [
            $user,
            $user2,
            $user3
        ];

        // catégories
        for ($i = 1; $i <= 20; $i ++) {
            $userNum = rand(0, 2);
            $owner = $users[$userNum];

            $category = new Category();
            $category->setName('Catégorie ' . ($userNum + 1) . '-' . $i);
            $category->setPosition($i * 10);
            $category->setUser($owner);

            // notes
            for ($j = 1; $j <= 15; $j++) {
                $note = new Note();
                $note->setTitle('Note ' . ($userNum + 1) . '-' . $i. '-' . $j);
                $note->setContent($faker->realText());
                $note->setVisibility($visibility[rand(0, 2)]);
                $note->setUser($owner);
                $manager->persist($note);

                // jointure
                $noteCategory = new NoteCategory();
                $noteCategory->setNote($note);
                $noteCategory->setCategory($category);
                $noteCategory->setPosition($j * 10);
                $manager->persist($noteCategory);

                $note->addNoteCategory($noteCategory);
                $category->addNoteCategory($noteCategory);
            }

            $manager->persist($category);
        }

        $manager->flush();
    }
}
