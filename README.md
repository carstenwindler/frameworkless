# Frameworkless Webservice

This repository is an example about how to code a simple webservice in PHP without the use of frameworks. 

> Please note, this is not meant to be used in production. Use it on your own risk.

## V4 - adding tests and code quality tools

Version 4 is finally adding basic functional API tests and Code Quality tools.

### Installation

```
docker-compose up -d
make composer-install
make mysql-import build/mysql/database.sql
```

### Usage 
```
curl -u username:password -X GET http://localhost:8080/products 
curl -u username:password -X GET http://localhost:8080/products/1
curl -u username:password -X POST localhost:8080/products -d '{ "description": "new item" }' -H "Content-Type: application/json"
curl -u username:password -X PUT localhost:8080/products/2 -d '{ "description": "updated" }' -H "Content-Type: application/json"
curl -u username:password -X DELETE localhost:8080/products/2
```

### Tests

* `make test` to execute the default test suite.

### Code Quality

* `make cs` - run Code Sniffer
* `make csfix` - run Code Sniffer Fixer
* `make analyze` - run Code Analyzer

