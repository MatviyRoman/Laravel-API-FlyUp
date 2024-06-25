#!/bin/bash

docker-compose up -d
docker-compose exec php bash -c "
    php artisan migrate
"
