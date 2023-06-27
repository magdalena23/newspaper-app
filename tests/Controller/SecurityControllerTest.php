<?php

namespace Controller;

use App\Controller\SecurityController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLogin(): void
    {
        $client = static::createClient();

        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Zaloguj się');
    }

    public function testLogout(): void
    {
        $this->expectException(\LogicException::class);
        $securityController = new SecurityController();
        $securityController->logout();
    }
}