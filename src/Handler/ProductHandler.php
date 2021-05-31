<?php

declare(strict_types=1);

namespace MyMicroService\Handler;

use Doctrine\DBAL\Connection;
use League\Route\Http\Exception\BadRequestException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class ProductHandler implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private Connection $connection
    ) {
    }

    public function get(): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT * FROM products'
        );
    }

    public function getById(ServerRequestInterface $request, array $args): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT * FROM products WHERE id = ?',
            [(int) $args['id']]
        );
    }

    public function delete(ServerRequestInterface $request, array $args): array
    {
        $this->connection->executeStatement(
            'DELETE FROM products WHERE id = ?',
            [(int) $args['id']]
        );

        return [];
    }

    public function post(ServerRequestInterface $request): array
    {
        $parameters = json_decode($request->getBody()->getContents(), true);

        if (!isset($parameters['description'])) {
            throw new BadRequestException();
        }

        $this->connection->executeStatement(
            'INSERT INTO products (description) VALUES (?)',
            [$parameters['description']]
        );

        return [
            'id' => $this->connection->lastInsertId(),
        ];
    }

    public function put(ServerRequestInterface $request, array $args): array
    {
        $parameters = json_decode($request->getBody()->getContents(), true);

        if (!isset($parameters['description'])) {
            throw new BadRequestException();
        }

        $this->connection->executeStatement(
            'UPDATE products SET description = ? WHERE id= ?',
            [$request['description'],(int) $args['id']]
        );

        return [];
    }

}
