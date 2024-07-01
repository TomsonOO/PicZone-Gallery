# Backend API Documentation

## Viewing API Documentation

To view the API documentation for the backend, you can use Swagger UI with Docker. 

Run the following Docker command to start Swagger UI:

```bash
docker run -p 8082:8080 -e SWAGGER_JSON=/api-docs/openapi.yaml -v $(pwd)/api-docs:/api-docs swaggerapi/swagger-ui
```


## Running Tests

The project includes unit tests and integration tests. You can run these tests through a provided script that manages the Docker environment for testing.


### Test Commands

The script supports the following commands to manage tests:

- **Start the test environment**: This command sets up and starts all services required for testing but does not execute tests automatically.

  ```bash
  ./start_test_compose.sh
### Script Command Arguments

- `start`: Initializes and starts all necessary services for testing, without automatically executing any tests.
- `stop`: Stops all services and cleans up the test environment.
- `test-unit`: Runs all unit tests and then shuts down the test environment.
- `test-integration`: Executes all integration tests and then stops all services.
- `test-all`: Runs both unit and integration tests, starting the environment beforehand and stopping it afterwards.
