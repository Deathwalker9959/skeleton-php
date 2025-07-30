<?php

namespace App;

use Skeleton\Singletons\ConnectionSingleton;
use Skeleton\Singletons\QueryBuilderSingleton;
use Skeleton\Singletons\RouterSingleton;
use Skeleton\Singletons\FileStorageSingleton;
use PDOException;

// Load Composer's autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Define application constants
define("APP_DIR", __DIR__);
define("ROUTES_DIR", __DIR__ . '/routes/');
define("MODELS_DIR", __DIR__ . '/app/Models/');
define("MIDDLEWARE_DIR", __DIR__ . '/app/Middleware/');
define("CONTROLLERS_DIR", __DIR__ . '/app/Controllers/');
define("SERVICES_DIR", __DIR__ . '/app/Services/');
define("VIEWS_DIR", __DIR__ . '/resources/views/');
define("ASSETS_DIR", __DIR__ . '/public/assets/');
define("STORAGE_PATH", __DIR__ . '/storage');
define("APP_CONFIG", require_once(__DIR__ . '/config.php'));
define("TIME_FORMAT", 'Y-m-d H:i:s');

class Autoloader
{
    /**
     * Initialize the application
     */
    public static function initializeApplication(): void
    {
        // Initialize singletons
        try {
            ConnectionSingleton::getInstance();
            QueryBuilderSingleton::getInstance();
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            http_response_code(500);
            exit("Database connection failed");
        }

        RouterSingleton::getInstance();
        FileStorageSingleton::getInstance();
    }
}

// Initialize the application
Autoloader::initializeApplication();
