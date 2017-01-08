#!/bin/bash

# We need to install dependencies only for Docker
[[ ! -e /.dockerenv ]] && [[ ! -e /.dockerinit ]] && exit 0

set -xe

# Install mysql driver
# Here you can install any other extension that you need
docker-php-ext-install pdo_mysql
#docker-php-ext-install gd