#!/bin/bash

git clone https://github.com/junshi-tech/promotion-backstage.git tmp 
cp -fr tmp/* . &&  rm -fr tmp
docker-compose up -d && docker cp php/redis.so fpm:/opt/bitnami/php/lib/php/extensions/ && docker restart fpm
echo 'Update app successed.'
