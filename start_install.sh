#!/bin/bash

docker-compose up -d

docker-compose exec db bash -c "
    MYSQL_PWD='root' mysql -u root -e 'CREATE DATABASE IF NOT EXISTS flyup_db;'
"

docker-compose exec php bash -c "
    composer update &&
    composer install &&
    php artisan migrate:fresh &&
    php artisan migrate
"
