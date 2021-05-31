# Frameworkless Webservice

This repository is an example about how to code a simple webservice in PHP without the use of frameworks. 

> Please note, this is not meant to be used in production. Use it on your own risk.

## V2 - using some basic packages

Version 2 of this little webservice is introducing composer and some neat packages for DB abstraction, Routing and HTTP Request/Response handling. 

### Installation

```
docker-compose up -d
make mysql-import build/mysql/database.sql
```

### Usage 
```
curl -X GET http://localhost:8080/product
curl -X GET http://localhost:8080/product/1
curl -X POST localhost:8080/product -d '{ "description": "new item" }' -H "Content-Type: application/json"
curl -X PUT localhost:8080/product/2 -d '{ "description": "updated" }' -H "Content-Type: application/json"
curl -X DELETE localhost:8080/product/2
```