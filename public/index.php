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
// Controlles
//
function getProductsController($request): array
{
    global $conn;

    $statement = $conn->prepare('SELECT id, description FROM products');
    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function getProductsByIdController($request): array
{
    global $conn;

    $statement = $conn->prepare('SELECT id, description FROM products WHERE id = ?');
    $statement->execute([ (int) $request[0] ]);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function postProductsController($request): array
{
    global $conn;

    $statement = $conn->prepare('INSERT INTO products (description) VALUES (?)');

    $statement->execute([ $request['description'] ]);

    return [
        'id' => $conn->lastInsertId()
    ];
}

function putProductsController($request): array
{
    global $conn;

    $statement = $conn->prepare('UPDATE products SET description = ? WHERE id= ?');
    $statement->execute([ $request['description'], (int) $request[0] ]);

    return [];
}

function deleteProductsController($request): array
{
    global $conn;

    $statement = $conn->prepare('DELETE FROM products WHERE id= ?');
    $statement->execute([ (int) $request[0] ]);

    return [];
}

//
// Route Config & Dispatch
//
$match = matchRoute([
    [
        'method' => 'GET',
        'url' => '/products',
        'callback' => 'getProductsController'
    ],
    [
        'method' => 'POST',
        'url' => '/products',
        'callback' => 'postProductsController'
    ],
    [
        'method' => 'GET',
        'url' => '/products/:id',
        'callback' => 'getProductsByIdController'
    ],
    [
        'method' => 'PUT',
        'url' => '/products/:id',
        'callback' => 'putProductsController'
    ],
    [
        'method' => 'DELETE',
        'url' => '/products/:id',
        'callback' => 'deleteProductsController'
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
