<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiTest extends WebTestCase
{

    public function testApiLogin() : void
    {
        $client = static::createClient();
        $response = $client->request(
            'POST',
            'api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username":"dubuque.elian@durgan.com","password":"dubuque.elian@durgan.com"}'
        );

        $response = $client->getResponse();

        $token = json_decode($response->getContent(), true)['token'];
        dump($token);

        $this->assertResponseIsSuccessful();
    }


    public function testApiRegister(): void
    {
        $client = static::createClient();
        $response = $client->request(
            'POST',
            'api/user/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"email":"test@durgan.com","password":"test@durgan.com"}'
        );

        $this->assertResponseIsSuccessful();
    }

    public function testApiCreatePost() : void
    {
        //Get token
        $client = static::createClient();
        $response = $client->request(
            'POST',
            'api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username":"dubuque.elian@durgan.com","password":"dubuque.elian@durgan.com"}'
        );

        $response = $client->getResponse();

        $token = json_decode($response->getContent(), true)['token'];
        
        $this->assertSame(200, $response->getStatusCode());

        //Create Post
        $response = $client->request(
            'POST',
            '/api/post/new',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $token],
            '{"title":"New title","content":"new content post"}'
        );

        $response = $client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertSame(200, $response->getStatusCode());
    }

}
