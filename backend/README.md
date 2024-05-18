# Backend API Documentation

## Viewing API Documentation

To view the API documentation for the backend, you can use Swagger UI with Docker. 

Run the following Docker command to start Swagger UI:

```bash
docker run -p 8082:8080 -e SWAGGER_JSON=/api-docs/openapi.yaml -v $(pwd)/api-docs:/api-docs swaggerapi/swagger-ui
