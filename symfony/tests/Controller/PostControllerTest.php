<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    public function testShowPosts(): void
    {
        $client = static::createClient();

        $client->request('GET', '/posts');

        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

    }
}