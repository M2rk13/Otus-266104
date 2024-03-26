#!/bin/bash

# prepare rabbit-mq
docker compose -p otus-266104 exec rabbitmq rabbitmqadmin declare exchange name=main_exchange \
type=direct -u rmuser -p rmpassword
docker compose -p otus-266104 exec -d webserver php launch-rabbit.php

# prepare database
dockerMysqlIP=$(docker inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' otus-266104-mysql-1)

docker compose -p otus-266104 exec mysql ./bin/init-db-1.sh "$dockerMysqlIP"
docker compose -p otus-266104 exec mysql ./bin/init-db-2.sh "$dockerMysqlIP"
docker compose -p otus-266104 exec mysql ./bin/init-db-3.sh "$dockerMysqlIP"
