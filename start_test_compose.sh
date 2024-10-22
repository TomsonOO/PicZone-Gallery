#!/bin/bash

load_env() {
    export $(cat backend/.env.test | grep -v '^#' | xargs)
}

start_env() {
    load_env
    docker compose -f compose.test.yaml up -d  > /dev/null 2>&1
    echo "Test environment started."
}

stop_env() {
    load_env
    docker compose -f compose.test.yaml down > /dev/null 2>&1
    echo "Test environment stopped."
}

run_unit_tests() {
    echo "Running unit tests..."
    docker compose -f compose.test.yaml run php-fpm php bin/phpunit --testsuite unit
}

run_integration_tests() {
    echo "Running integration tests..."
    docker compose -f compose.test.yaml run php-fpm php bin/phpunit --testsuite integration
}

run_all_tests() {
    echo "Running all tests..."
    docker compose -f compose.test.yaml run php-fpm php bin/phpunit
}

case "$1" in
    start)
        start_env
        ;;
    stop)
        stop_env
        ;;
    test-unit)
        start_env
        run_unit_tests
        stop_env
        ;;
    test-integration)
        start_env
        run_integration_tests
        stop_env
        ;;
    test-all)
        start_env
        run_all_tests
        stop_env
        ;;
    *)
        echo "Usage: $0 {start|stop|test-unit|test-integration|test-all}"
        exit 1
        ;;
esac
