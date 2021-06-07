<?php

declare(strict_types=1);

namespace MyMicroService\Test\Api;

use Doctrine\DBAL\Connection;
use GuzzleHttp\Client;
use Laminas\Diactoros\ServerRequestFactory;
use MyMicroService\Database\DatabaseConnectionFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class ProductControllerTest extends TestCase
{
    private Client $client;
    private array $auth;
    private Connection $connection;

    private function getResponseAsArray(ResponseInterface $response): array
    {
        return json_decode($response->getBody()->getContents(), true);
    }

    public function setUp(): void
    {
        $this->client = new Client();
        $this->auth = ['auth' => [ getenv('AUTH_USERNAME'), getenv('AUTH_PASSWORD')]];

        $this->connection = (new DatabaseConnectionFactory())(ServerRequestFactory::fromGlobals());
        $this->connection->executeStatement(file_get_contents(getcwd() . '/tests/fixtures/database.sql'));
    }

    public function testGetProduct()
    {
        $response = $this->client->request(
            'GET',
            'http://web/products',
            $this->auth
        );

        $this->assertEquals(
            [
                [
                    'id' => 1,
                    'description' => 'PHP Handbook'
                ]
            ],
            $this->getResponseAsArray($response)
        );
    }

    public function testPostProduct()
    {
        $post = [ 'json' => [ 'description' => 'unittest' ]];

        $response = $this->client->request(
            'POST',
            'http://web/products',
            array_merge($post, $this->auth)
        );

        $this->assertEquals(
            [
                'id' => 2,
            ],
            $this->getResponseAsArray($response)
        );

        $result = $this->connection->fetchAllAssociative(
            'SELECT id, description FROM products'
        );

        $this->assertEquals(
            [
                [
                    'id' => 1,
                    'description' => 'PHP Handbook'
                ],
                [
                    'id' => 2,
                    'description' => 'unittest'
                ]
            ],
            $result
        );
    }
}
