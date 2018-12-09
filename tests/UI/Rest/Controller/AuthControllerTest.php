<?php

namespace App\Tests\UI\Rest\Controller;

use Symfony\Component\HttpFoundation\Response;

class AuthControllerTest extends ApiTestCase
{
    public function testRequiresAuthentication()
    {
        $this->post('/api/articles', []);
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testBadToken()
    {
        $this->client->request(
            'POST',
            '/api/articles',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_Authorization' => 'Bearer WRONG'
            ]
        );
        $response = $this->client->getResponse();
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('application/json', $this->client->getResponse()->headers->get('Content-Type'));
    }

    public function testRegister(): void
    {

        $this->post('/api/register', [
            'username' => 'new_username',
            'password' => 'test_pass',
        ]);

        self::assertSame(201, $this->client->getResponse()->getStatusCode());
        $this->asserter()->assertResponsePropertiesExist(
            $this->client->getResponse(),
            ['username', 'id']
        );
    }

    public function testFailGetToken(): void
    {
        $this->post('/api/auth_check', [
            'username' => 'fake_username',
            'password' => 'fake_password',
        ]);

        self::assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccessGetToken(): void
    {
        $this->createUser();

        $this->post('/api/auth_check', [
            'username' => self::DEFAULT_USERNAME,
            'password' => self::DEFAULT_PASS,
        ]);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->asserter()->assertResponsePropertyExists(
            $this->client->getResponse(),
            'token'
        );
    }
}
