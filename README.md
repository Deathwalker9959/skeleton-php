# Skeleton PHP Framework

Skeleton is a sole written, lightweight PHP framework designed to provide the barebones structure for building web applications. It includes essential features such as basic routing, a query builder (ORM), and other utilities to simplify development while maintaining flexibility.

## Features

- **Routing**: A simple and intuitive routing system to handle HTTP requests and map them to controllers.
- **Query Builder (ORM)**: A basic query builder to interact with the database using an object-oriented approach.
- **Singleton Pattern**: Implements the singleton pattern for shared resources like database connections.
- **Middleware Support**: Interfaces for adding middleware to handle requests and responses.
- **Utility Functions**: Includes helper functions for common tasks like string manipulation and debugging.
- **PSR-4 Autoloading**: Follows PSR-4 standards for autoloading classes.

## Directory Structure

```
├── src/
│   ├── Database/          # Query builder and database utilities
│   ├── Router/            # Routing logic and middleware
│   ├── Globals/           # Global utility functions
│   ├── Models/            # Base model and ORM logic
│   ├── Singletons/        # Singleton implementations
│   └── Support/           # Additional support utilities
├── config/                # Configuration files
├── tests/                 # Unit and integration tests
├── vendor/                # Composer dependencies
└── public/                # Publicly accessible files (e.g., index.php)
```

## Getting Started

### Prerequisites

- PHP 8.1 or higher
- Composer
- MySQL / SQLite

### Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/Deathwalker9959/skeleton-php.git
   cd skeleton-php
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Configure your environment:
   - Update the `config/constants.php` file with your database and application settings.

4. Start the development server:
   ```bash
   php -S localhost:8000 -t public
   ```

5. Open your browser and navigate to `http://localhost:8000`.

## Usage

### Routing
Define your routes in the `config/routes/` directory. Example:

```php
$router->get('/home', [HomeController::class, 'index']);
$router->post('/submit', [FormController::class, 'submit']);
```

### Query Builder
Use the query builder to interact with your database:

```php
$queryBuilder = new QueryBuilder($pdo);
$users = $queryBuilder->table('users')->select(['id', 'name'])->get();
```

### Middleware
Implement the `MiddlewareInterface` to create custom middleware:

```php
class AuthMiddleware implements MiddlewareInterface {
    public function handle(Request $request, array $models): void {
        // Authentication logic
    }
}
```

## Testing

Run the test suite using PHPUnit:

```bash
vendor/bin/phpunit
```

## Contributing

Contributions are welcome! Feel free to fork the repository and submit a pull request.

## License

This project is licensed under the MIT License. See the LICENSE file for details.

## CLI Tool

Skeleton includes a CLI tool to streamline project creation and management. The tool is located in the `bin/` directory and provides the following commands:

### Create a New Project

To create a new Skeleton project:

```bash
bin/skeleton <directory>
```

This will create a new project in the specified directory with the necessary structure and an `index.php` file.

### Generate a Controller

To generate a new controller:

```bash
bin/skeleton create:controller <ControllerName>
```

This will create a new controller file in the `src/Controller/` directory.

### Generate a Docker Environment

To generate a Docker environment with all necessary configuration files:

```bash
bin/skeleton create:docker
```

This will create a `Docker/` directory with docker-compose.yml, Dockerfile, and Nginx configurations.

### Generate a Model

To generate a new model:

```bash
bin/skeleton create:model <ModelName>
```

This will create a new model file in the `Models/` directory.
