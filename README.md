# Frameworkless example webservice

## V1 - only PHP, no packages

> Please note, this is only an example how you could theoretically build a webservice. This is not how I would recommend it, though.

### Installation

```
docker-compose up -d
make mysql-import build/mysql/database.sql
```

### Usage 
```
curl http://localhost:8080/product
```