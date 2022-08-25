#!/usr/bin/env bash

echo "::::: SETUP STARTED :::::"


docker-compose build --force-rm --no-cache

docker-compose up --force-recreate -d

docker exec -i Router sh -c 'cd /etc/apache2/mods-available'<<<'a2enmod rewrite'

docker-compose down

echo "::::: SETUP COMPLETED :::::"
