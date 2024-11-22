#!/bin/bash

if [ -f .env ]; then
  export $(grep -v '^#' .env | xargs)
else
  echo ".env file not found. Please create it with the necessary environment variables."
  exit 1
fi

if [ -z "${ELASTIC_PASSWORD}" ]; then
  echo "Please set the ELASTIC_PASSWORD environment variable in the .env file"
  exit 1
fi

if [ -z "${KIBANA_PASSWORD}" ]; then
  echo "Please set the KIBANA_PASSWORD environment variable in the .env file"
  exit 1
fi

mkdir -p ./certs

# Run a temporary Elasticsearch container to generate certificates
docker run --rm \
  -v $(pwd)/certs:/usr/share/elasticsearch/config/certs \
  -u 0:0 \
  docker.elastic.co/elasticsearch/elasticsearch:${STACK_VERSION} \
  /bin/bash -c "
    # Generate CA if it doesn't exist
    if [ ! -f config/certs/ca.zip ]; then
      echo 'Generating CA'
      bin/elasticsearch-certutil ca --silent --pem -out config/certs/ca.zip
      unzip config/certs/ca.zip -d config/certs
    fi

    # Generate certificates for Elasticsearch and Kibana
    if [ ! -f config/certs/certs.zip ]; then
      echo 'Generating certificates for Elasticsearch and Kibana'
      echo -ne 'instances:\n  - name: es01\n    dns:\n      - es01\n      - localhost\n    ip:\n      - 127.0.0.1\n  - name: kibana\n    dns:\n      - kibana\n      - localhost\n    ip:\n      - 127.0.0.1\n' > config/certs/instances.yml
      bin/elasticsearch-certutil cert --silent --pem -out config/certs/certs.zip --in config/certs/instances.yml --ca-cert config/certs/ca/ca.crt --ca-key config/certs/ca/ca.key
      unzip config/certs/certs.zip -d config/certs
    fi

    # Set file permissions
    echo 'Setting file permissions'
    chown -R 1000:1000 config/certs
    find config/certs -type d -exec chmod 750 {} \;
    find config/certs -type f -exec chmod 640 {} \;
  "

echo "Certificates have been generated and are located in the ./certs directory"
