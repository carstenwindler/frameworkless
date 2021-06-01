<?php

declare(strict_types=1);

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Laminas\Diactoros\ResponseFactory;
use MyMicroService\Controller\ProductController;

require '../vendor/autoload.php';

//
// create request instance
//
$request = Laminas\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

//
// Setup container
//
$container = new League\Container\Container();
$container
    ->add(ProductController::class)
    ->addArgument(Connection::class);
$container
    ->add(Connection::class, function (): Connection {
        //
        // DB Connection
        //
        $connectionParams = [
            'dbname' => getenv('MYSQL_DATABASE'),
            'user' => getenv('MYSQL_USER'),
            'password' => getenv('MYSQL_PASSWORD'),
            'host' => getenv('MYSQL_HOST'),
            'driver' => 'pdo_mysql',
            'port' => getenv('MYSQL_PORT'),
        ];

        return DriverManager::getConnection($connectionParams);
    });

//
// Initialise router
//
$responseFactory = new ResponseFactory();
$strategy = new League\Route\Strategy\JsonStrategy($responseFactory);
$strategy->setContainer($container);
$router = new League\Route\Router();
$router->setStrategy($strategy);

//
// Configure routes
//
$router->map(
    'GET',
    '/products',
    [ProductController::class, 'get']
);

$router->map(
    'GET',
    '/products/{id}',
    [ProductController::class, 'getById']
);

$router->map(
    'POST',
    '/products',
    [ProductController::class, 'post']
);

$router->map(
    'DELETE',
    '/products/{id}',
    [ProductController::class, 'delete']
);

$router->map(
    'PUT',
    '/products/{id}',
    [ProductController::class, 'put']
);

//
// Dispatch the request to receive a response object
//
$response = $router->dispatch($request);

//
// Finally send the response
//
(new Laminas\HttpHandlerRunner\Emitter\SapiEmitter())->emit($response);
