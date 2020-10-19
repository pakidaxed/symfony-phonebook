<?php

namespace App\tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    /**
     * Testing all the way from first page, logging in, looking for logout button, which confirms
     * successful authentication, and logging out, also checking if we are redirected the right way
     */
    public function testIndex()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html h1.title', 'PhoneBook APP');

        $login_link = $crawler->filter('a:contains("Login")')->link();
        $crawler = $client->click($login_link);

        $form = $crawler->selectButton('Login')->form();

        ///////////////// WARNING /////////////////////////////
        // Enter existing users credentials to pass the test
        // Previously it was made with fixtures, but after deployment there is no users in DB at all
        $form['email'] = 'testuser1@testserver1.com';
        $form['password'] = 'TestPassword123';
        ////////////////////////////////////////////////////////
        ///
        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('/contacts/');

        $crawler = $client->request('GET', '/contacts/');
        $logout_link = $crawler->filter('a:contains("Logout")')->link();
        $client->click($logout_link);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('');

    }
}