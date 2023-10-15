<?php

namespace App\DataFixtures;

use App\Entity\Posts;
use Faker\Factory;
use App\Entity\User;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PostsFixtures extends Fixture
{

    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for($i = 1; $i <= 3; $i++){
            $user = new User();
            $email = $faker->email;
            $user->setEmail($email);
            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    $email // password == email
                )
            );

            $manager->persist($user);

            for($p = 1; $p <= 5; $p++){
                $post = new Posts();
                $post->setTitle($faker->sentence(10));
                $post->setContent($faker->text(1000));
                $date = $faker->numberBetween(-100, -2);
                $post->setCreated( new DateTime($date.' days'));
                $post->setAuthor($user);

                $manager->persist($post);
            }
        }

        $manager->flush();
    }
}
