#!/bin/bash

load_env() {
    export $(cat .env.test | grep -v '^#' | xargs)
}

case "$1" in
    start)
        load_env
        docker compose -f compose.test.yaml up -d
        echo "Test environment started."
        ;;
    stop)
        load_env
        docker compose -f compose.test.yaml down
        echo "Test environment stopped."
        ;;
    *)
        echo "Usage: $0 {start|stop}"
        exit 1
        ;;
esac
