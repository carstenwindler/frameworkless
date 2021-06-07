<?php

declare(strict_types=1);

namespace MyMicroService\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Psr\Http\Message\RequestInterface;

class DatabaseConnectionFactory
{
    public function __invoke(RequestInterface $request): Connection
    {
        $port = $request->getUri()->getPort();

        if ($port === 8081) {
            $connectionParams = [
                'driver' => 'pdo_sqlite',
                'memory' => '/tmp/api-test.db',
            ];
        } else {
            $connectionParams = [
                'dbname' => getenv('MYSQL_DATABASE'),
                'user' => getenv('MYSQL_USER'),
                'password' => getenv('MYSQL_PASSWORD'),
                'host' => getenv('MYSQL_HOST'),
                'driver' => 'pdo_mysql',
                'port' => getenv('MYSQL_PORT'),
            ];
        }

        return DriverManager::getConnection($connectionParams);
    }
}
