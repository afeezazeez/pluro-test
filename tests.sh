#!/bin/bash
set -e

GREEN=$(tput setaf 2)

echo "${GREEN}Running app tests ..."

docker-compose exec app ./vendor/bin/phpunit
