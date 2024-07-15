#!/bin/bash

show_usage() {
    echo "Usage: $0 [up|down] [--build] [--remove-orphans] [--no-cache] [-d]"
}

COMMAND="up"
BUILD=""
REMOVE_ORPHANS=""
NO_CACHE=""
DETACHED=""

while [[ "$#" -gt 0 ]]; do
    case $1 in
        up|down) COMMAND=$1;;
        --build) BUILD="--build";;
        --remove-orphans) REMOVE_ORPHANS="--remove-orphans";;
        --no-cache) NO_CACHE="--no-cache";;
        -d) DETACHED="-d";;
        *) show_usage; exit 1;;
    esac
    shift
done

if [[ "$COMMAND" == "up" ]]; then
    docker compose -f compose.yaml -f compose.elasticsearch.yaml up $BUILD $REMOVE_ORPHANS $NO_CACHE $DETACHED
elif [[ "$COMMAND" == "down" ]]; then
    docker compose -f compose.yaml -f compose.elasticsearch.yaml down $REMOVE_ORPHANS
else
    show_usage
    exit 1
fi
