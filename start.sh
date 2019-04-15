#!/bin/bash

git pull upstream master && docker-compose up -d && docker cp container/php/redis.so fpm:/opt/bitnami/php/lib/php/extensions/ && docker restart fpm
echo 'Update app successed.'
