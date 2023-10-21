<?php

namespace App\Tests\Controller;

use App\Entity\Posts;
use App\Repository\PostsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Latest Posts');
    }

    public function testCreatePost() : void 
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        
        $testUser = $userRepository->findOneByEmail('hconroy@kulas.com');
        $client->loginUser($testUser);

        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $post = new Posts();
        $post->setAuthor($testUser);
        $post->setTitle('This Title');
        $post->setContent('Content lorem ipsum');
        $post->setCreated(new \DateTime());


        $entityManager->persist($post);
        $entityManager->flush();

        $client->request('GET', '/post/'.$post->getId().'/edit');

        $this->assertInputValueSame('post[title]', 'This Title');
    }

    public function testPostsCount(): void
    {
        $postsRepository = static::getContainer()->get(PostsRepository::class);
        $totalPosts = $postsRepository->createQueryBuilder('p')
            ->select('count(p.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertEquals(15, $totalPosts);
    }

}
