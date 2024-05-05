# Backend API Documentation

## Generating API Documentation

To generate the OpenAPI specification for the backend API, run the following command within the Docker container:

```bash
docker-compose exec backend ./vendor/bin/openapi src -o openapi.yaml