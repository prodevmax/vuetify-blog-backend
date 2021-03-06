<?php

declare(strict_types=1);

namespace App\Tests\UI\Rest\Controller;

use App\Domain\User\User;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class ApiTestCase extends WebTestCase
{
    public const DEFAULT_USERNAME = 'user12345';

    public const DEFAULT_PASS = '1234567890';

    /** @var ResponseAsserter */
    private $responseAsserter;

    /** @var null|Client */
    protected $client;

    /** @var null|string */
    protected $token;

    protected function setUp()
    {
        $this->client = static::createClient();
        $this->purgeDatabase();
    }

    protected function createUser(string $username = self::DEFAULT_USERNAME, string $plainPassword = self::DEFAULT_PASS): User
    {
        $user = new User($username);
        $password = $this->getService('security.password_encoder')
            ->encodePassword($user, $plainPassword);
        $user->setPassword($password);
        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();
        return $user;
    }

    protected function post(string $uri, array $params): void
    {
        $this->client->request(
            'POST',
            $uri,
            [],
            [],
            $this->getHeaders(),
            json_encode($params)
        );
    }

    protected function put(string $uri, array $params): void
    {
        $this->client->request(
            'PUT',
            $uri,
            [],
            [],
            $this->getHeaders(),
            json_encode($params)
        );
    }

    protected function get(string $uri, array $parameters = []): void
    {
        $this->client->request(
            'GET',
            $uri,
            $parameters,
            [],
            $this->getHeaders()
        );
    }

    protected function auth(string $username = self::DEFAULT_USERNAME, string $password = self::DEFAULT_PASS): void
    {
        $this->post('/api/auth_check', [
            'username' => $username,
            'password' => $password,
        ]);

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->token = $response['token'];
    }

    protected function logout(): void
    {
        $this->token = null;
    }

    private function getHeaders(): array
    {
        $headers = [
            'CONTENT_TYPE' => 'application/json',
            'ACCEPT' => 'application/json'
        ];
        if ($this->token) {
            $headers['HTTP_Authorization'] = sprintf('Bearer %s', $this->token);
        }
        return $headers;
    }

    protected function tearDown()
    {
        $this->client = null;
        $this->token = null;
    }

    protected function getEntityManager(): EntityManager
    {
        return $this->getService('doctrine.orm.entity_manager');
    }

    private function purgeDatabase(): void
    {
        $purger = new ORMPurger($this->getService('doctrine.orm.default_entity_manager'));
        $purger->purge();
    }

    protected function getService($id)
    {
        return self::$kernel->getContainer()
            ->get($id);
    }

    protected function asserter(): ResponseAsserter
    {
        if ($this->responseAsserter === null) {
            $this->responseAsserter = new ResponseAsserter();
        }

        return $this->responseAsserter;
    }



}
