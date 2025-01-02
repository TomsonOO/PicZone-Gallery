#!/bin/bash

load_env() {
    export $(cat backend/.env.test | grep -v '^#' | xargs)
}

build_env() {
    load_env
    docker compose -f compose.test.yaml --env-file .env.test build
    echo "Test environment built."
}

start_env() {
    load_env
    docker compose -f compose.test.yaml --env-file .env.test up -d  > /dev/null 2>&1
    echo "Test environment started."
    wait_for_elasticsearch
}

start_env_without_elastic() {
    load_env
    docker compose -f compose.test.yaml --env-file .env.test up -d  > /dev/null 2>&1
    echo "Test environment started."
}

stop_env() {
    load_env
    docker compose -f compose.test.yaml down > /dev/null 2>&1
    echo "Test environment stopped."
}

run_unit_tests() {
    echo "Running unit tests..."
    docker compose exec backend php bin/phpunit --testsuite unit
}

run_integration_tests() {
    echo "Running integration tests..."
    docker compose exec backend php bin/phpunit --testsuite integration
}

run_all_tests() {
    echo "Running all tests..."
    docker compose exec backend php bin/phpunit
}

wait_for_elasticsearch() {
  echo "Waiting for Elasticsearch to be ready (should take around 20 seconds)"
  local container_name="backend_test"
  local max_attempts=20
  local attempt=0
  while true; do
    if docker exec $container_name curl --silent --fail \
          -u "$ELASTIC_USERNAME:$ELASTIC_PASSWORD" \
          --cacert "$CA_CERT_PATH" \
          "$ELASTIC_URL"_cluster/health; then
      echo "Elasticsearch is up."
      break
    fi
    attempt=$((attempt+1))
    if [ "$attempt" -ge "$max_attempts" ]; then
      echo "Elasticsearch took too long to start."
      exit 1
    fi
    sleep 5
  done
}

case "$1" in
    build)
        build_env
        ;;
    start)
        start_env
        ;;
    stop)
        stop_env
        ;;
    test-unit)
        start_env_without_elastic
        run_unit_tests
        stop_env
        ;;
    test-unit-no-restart)
        run_unit_tests
        ;;
    test-integration)
        start_env
        run_integration_tests
        stop_env
        ;;
    test-integration-no-restart)
        run_integration_tests
        ;;
    test-all)
        start_env
        run_all_tests
        stop_env
        ;;
    test-all-no-restart)
        run_all_tests
        ;;
    *)
        echo "Usage: $0 {build|start|stop|test-unit|test-integration|test-all}"
        exit 1
        ;;
esac
