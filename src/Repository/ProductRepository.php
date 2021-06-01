<?php

declare(strict_types=1);

namespace MyMicroService\Repository;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class ProductRepository implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private Connection $connection
    ) {
    }

    public function getAll(): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT id, description FROM products'
        );
    }

    public function get(int $id): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT id, description FROM products WHERE id = ?',
            [$id]
        );
    }

    public function delete(int $id): int
    {
        return $this->connection->executeStatement(
            'DELETE FROM products WHERE id = ?',
            [$id]
        );
    }

    public function add(string $description): int
    {
        $this->connection->executeStatement(
            'INSERT INTO products (description) VALUES (?)',
            [$description]
        );

        return (int) $this->connection->lastInsertId();
    }

    public function update(int $id, string $description): int
    {
        return $this->connection->executeStatement(
            'UPDATE products SET description = ? WHERE id= ?',
            [$description, $id]
        );
    }

}
