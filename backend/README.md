# Backend API Documentation

## 📄 Viewing API Documentation

⚠️ **API Documentation Update In Progress**: The API documentation is currently outdated due to the recent architecture refactor to **Hexagonal Architecture** and **CQRS**. The documentation will be updated soon to reflect these changes.

In the meantime, you can still view the existing documentation using Swagger UI with Docker, though it may not be fully accurate.

Run the following Docker command to start Swagger UI:

```bash
docker run -p 8082:8080 -e SWAGGER_JSON=/api-docs/openapi.yaml -v $(pwd)/api-docs:/api-docs swaggerapi/swagger-ui
```

## ✅ Running Tests

The project includes both **unit tests** and **integration tests**. These tests can be run through a script that manages the Docker environment for testing.

⚠️ **Note**: The tests are currently being updated due to the project's transition to **Hexagonal Architecture**. Some tests may not be fully implemented yet.

### 💻 Test Commands

The script supports the following commands:

- **Start the test environment**: Sets up and starts all necessary services for testing without executing the tests automatically.

  ```bash
  ./start_test_compose.sh
  
### 🔧 Script Command Arguments

- `start`: Initializes and starts all necessary services for testing without running tests automatically.
- `stop`: Stops all services and cleans up the test environment.
- `test-unit`: Runs all unit tests and then shuts down the test environment.
- `test-integration`: Executes all integration tests and then stops all services.
- `test-all`: Runs both unit and integration tests, starting the environment beforehand and stopping it afterward.

## 🐞 Xdebug

To enable **Xdebug**, set the following environment variable in your `.env` file:

```env
XDEBUG_CLIENT_HOST=      # Set to 'host.docker.internal' for Windows/Mac or your host IP for Linux
```

Ensure you configure path mappings for your project. Below is an example using PHPStorm, where the local directory maps to `/var/www` in the container:

![Path Mapping Example](../readme_screesnshots/xdebugger_path_mappings.png)

