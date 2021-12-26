#!/bin/bash

function printLine {
    echo $1;
}

function runScript {
    docker exec -it pmus-php "$@";
}

# Check if the .env.example file exists, if not try to copy the .local.env to .env.example
if ! [ -f ".env" ];
then
    ENVIRONMENT_FILE=".env.example";
    printLine "==[ Copying $ENVIRONMENT_FILE to .env"
    cp $ENVIRONMENT_FILE .env.example
    local_uid=$(id -u);
    local_gid=$(id -g);

    echo "" >> ".env"
    echo "DOCKER_UID=$local_uid" >> ".env"
    echo "DOCKER_GID=$local_gid" >> ".env"
fi

echo "==[ DOCKER INIT ]==";
docker-compose up -d --remove-orphans

printLine "==[ Install composer"
runScript composer install

