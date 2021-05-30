<?php

//
// DB Connection
//
$dsn = sprintf(
    'mysql:dbname=%s;host=%s:%s',
    getenv('MYSQL_DATABASE'),
    getenv('MYSQL_HOST'),
    getenv('MYSQL_PORT'),
);

$conn = new PDO($dsn, getenv('MYSQL_USER'), getenv('MYSQL_PASSWORD'));

//
// Router
// see https://stackoverflow.com/questions/11722711/url-routing-regex-php
//
function matchRoute(array $routes = []): array
{
    $reqMet = $_SERVER['REQUEST_METHOD'];
    $reqUrl = rtrim($_SERVER['REQUEST_URI'], "/");
    $postParams = json_decode(file_get_contents('php://input'), true) ?? [];

    foreach ($routes as $route) {
        $pattern = "@^" . preg_replace('/:[a-zA-Z0-9\_\-]+/', '([a-zA-Z0-9\-\_]+)', $route['url']) . "$@D";
        $params = [];
        $match = preg_match($pattern, $reqUrl, $params);

        if ($reqMet == $route['method'] && $match) {
            array_shift($params);
            $params = array_merge($params, $postParams);
            return [$route, $params];
        }
    }

    return [];
}

//
// Handler
//
function getProductHandler($request): array
{
    global $conn;

    $statement = $conn->prepare('SELECT * FROM products');
    $statement->execute();

    return $statement->fetchAll();
}

function getProductByIdHandler($request): array
{
    global $conn;

    $statement = $conn->prepare('SELECT * FROM products WHERE id = ?');
    $statement->execute([ $request[0] ]);

    return $statement->fetchAll();
}

function postProductHandler($request): array
{
    global $conn;

    $statement = $conn->prepare('INSERT INTO products (description) VALUES (?)');
    $statement->execute([ $request['description'] ]);

    return [
        'id' => $conn->lastInsertId()
    ];
}

function putProductHandler($request): array
{
    global $conn;

    $statement = $conn->prepare('UPDATE products SET description = ? WHERE id= ?');
    $statement->execute([ $request['description'], $request[0] ]);

    return [];
}

//
// Route Config & Dispatch
//
$match = matchRoute([
    [
        'method' => 'GET',
        'url' => '/product',
        'callback' => 'getProductHandler'
    ],
    [
        'method' => 'POST',
        'url' => '/product',
        'callback' => 'postProductHandler'
    ],
    [
        'method' => 'GET',
        'url' => '/product/:id',
        'callback' => 'getProductByIdHandler'
    ],
    [
        'method' => 'PUT',
        'url' => '/product/:id',
        'callback' => 'putProductHandler'
    ]
]);

if (count($match) == 0) {
    header('HTTP/1.1 404 Not Found');
    exit();
}

list($route,$params) = $match;

//
// Call handler and return response
//
$responseArray = call_user_func_array($route['callback'], [$params]);

header('HTTP/1.1 200 OK');
header('Content-Type: application/json');

echo json_encode($responseArray);
