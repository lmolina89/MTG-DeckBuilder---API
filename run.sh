#!/bin/sh

certbot certonly --webroot --webroot-path /var/www/html --email lmolinamoreno@hotmail.com --agree-tos --no-eff-email -d mtgdeckbuilder.redirectme.net

docker-php-entrypoint apache2-foreground
