# Frameworkless Webservice

This repository is an example about how to code a simple webservice in PHP without the use of frameworks. 

> Please note, this is not meant to be used in production. Use it on your own risk.

## V1 - only PHP, no packages

Version 1 of this little webservice is not using any packages or libraries. 

### Installation

```
docker-compose up -d
make mysql-import build/mysql/database.sql
```

### Usage 
```
curl -X GET http://localhost:8080/products
curl -X GET http://localhost:8080/products/1
curl -X POST localhost:8080/products -d '{ "description": "new item" }' -H "Content-Type: application/json"
curl -X PUT localhost:8080/products/2 -d '{ "description": "updated" }' -H "Content-Type: application/json"
curl -X DELETE localhost:8080/products/2
```