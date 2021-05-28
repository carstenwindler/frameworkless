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
// Assign Handlers to Routes
//
$match = matchRoute([
    [
        'method' => 'GET',
        'url' => '/product',
        'callback' => function($request) use ($conn): array {
            $statement = $conn->prepare('SELECT * FROM products');
            $statement->execute();

            return $statement->fetchAll();
        }
    ],
    [
        'method' => 'POST',
        'url' => '/product',
        'callback' => function($request) use ($conn): array {
            $statement = $conn->prepare('INSERT INTO products (description) VALUES (?)');
            $statement->execute([ $request['description'] ]);

            return [
                'id' => $conn->lastInsertId()
            ];
        }
    ],
    [
        'method' => 'GET',
        'url' => '/product/:id',
        'callback' => function($request) use ($conn): array {
            $statement = $conn->prepare('SELECT * FROM products WHERE id = ?');
            $statement->execute([ $request[0] ]);

            return $statement->fetchAll();
        }
    ]
]);

list($route,$params) = $match;

$responseArray = call_user_func_array($route['callback'], [$params]);

header('HTTP/1.1 200 OK');
header('Content-Type: application/json');
echo json_encode($responseArray);

// no 404, no 500s ...
// no auth
// no logging
// no tests
// no pagination
// run speed comparison without xdebug