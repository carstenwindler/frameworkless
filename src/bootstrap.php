<?php

declare(strict_types=1);

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Laminas\Diactoros\ResponseFactory;
use MyMicroService\Handler\ProductHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\SyslogHandler;
use Monolog\Logger;

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
// Setup logger
//
$logger = new Logger('MyMircoService');
$formatter = new LineFormatter();
$handler = new SyslogHandler('MyMicroService', LOG_USER, Logger::INFO);
$handler->setFormatter($formatter);
$logger->pushHandler($handler);

//
// Setup container
//
$container = new League\Container\Container();
$container
    ->add(ProductHandler::class)
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
// Add Authentication Middleware
//
$router->middleware(
    new Middlewares\BasicAuthentication([
        getenv('AUTH_USERNAME') => getenv('AUTH_PASSWORD'),
    ])
);

//
// Configure routes
//
$router->map(
    'GET',
    '/product',
    [ProductHandler::class, 'get']
);

$router->map(
    'GET',
    '/product/{id}',
    [ProductHandler::class, 'getById']
);

$router->map(
    'POST',
    '/product',
    [ProductHandler::class, 'post']
);

$router->map(
    'DELETE',
    '/product/{id}',
    [ProductHandler::class, 'delete']
);

$router->map(
    'PUT',
    '/product/{id}',
    [ProductHandler::class, 'put']
);

//
// Dispatch the request to receive a response object
//
$response = $router->dispatch($request);

//
// Finally send the response
//
(new Laminas\HttpHandlerRunner\Emitter\SapiEmitter())->emit($response);
