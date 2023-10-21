<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('hconroy@kulas.com');
        $client->loginUser($testUser);
        $client->request('GET', '/dashboard');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('', 'You\'re logged in');
    }

    public function testProfileForm(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('hconroy@kulas.com');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/dashboard/profile/' . $testUser->getId());
        $this->assertSelectorTextContains('', 'Profile Info');

        $buttonCrawlerNode = $crawler->selectButton('btn-form-info');
        $form = $buttonCrawlerNode->form();
        $form['user_info[name]'] = 'Fabien11';
        $client->submit($form);

        ## Second option ###
        // $crawler = $client->submitForm('btn-form-info', [
        //     'user_info[name]' => 'new name2',
        // ]);

        $user = $userRepository->findOneBy([
            'email' => 'hconroy@kulas.com'
        ]);

        $this->assertSame('Fabien11', $user->getUserProfile()->getName());
    }
}
