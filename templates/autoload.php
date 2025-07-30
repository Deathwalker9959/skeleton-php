<?php

namespace App;

use App\ConnectionSingleton;
use App\QueryBuilderSingleton;
use App\RouterSingleton;
use App\FileStorageSingleton;

use PDOException;

define("APP_DIR", __DIR__);
define("GLOBALS_DIR", __DIR__ . '/vendor/globals/');
define("INTERFACES_DIR", __DIR__ . '/vendor/interfaces/');
define("BASE_DIR", __DIR__ . '/vendor/base/');
define("FACADES_DIR", __DIR__ . '/vendor/facades');
define("ROUTES_DIR", __DIR__ . '/Routes/');
define("MODELS_DIR", __DIR__ . '/Models/');
define("MIDDLEWARE_DIR", __DIR__ . '/Middleware/');
define("CONTROLLERS_DIR", __DIR__ . '/Controllers/');
define("SERVICES_DIR", __DIR__ . '/Services/');
define("VIEWS_DIR", __DIR__ . '/Views/');
define("ASSETS_DIR", __DIR__ . '/public/assets/');
define("STORAGE_PATH", __DIR__ . '/public/storage');
define("APP_CONFIG", require_once(__DIR__ . '/config.php'));
define("TIME_FORMAT", 'Y-m-d H:i:s');

class Autoloader
{
    static public function loadGlobals()
    {
        $files = array_diff(scandir(GLOBALS_DIR), ['.', '..']);
        array_walk($files, function ($file) {
            require_once GLOBALS_DIR . $file;
        });
    }

    static public function register()
    {
        // Register an autoload function for the 'vendor/interfaces' directory
        spl_autoload_register(function ($class) {
            $namespace = __NAMESPACE__ . '\\Interfaces\\';
            $namespaceLen = strlen($namespace);
            if (strcmp(substr($class, 0, $namespaceLen), __NAMESPACE__ . '\\Interfaces\\') !== 0)
                return;

            $file = INTERFACES_DIR . str_replace(__NAMESPACE__ . '\\Interfaces\\', '/', $class) . '.php';
            if (file_exists($file)) {
                require_once $file;
            }
        });

        // Register an autoload function for the 'Models' directory
        spl_autoload_register(function ($class) {
            $namespace = __NAMESPACE__ . '\\Models\\';
            $namespaceLen = strlen($namespace);
            if (strcmp(substr($class, 0, $namespaceLen), __NAMESPACE__ . '\\Models\\') !== 0)
                return;
            $file = MODELS_DIR . str_replace('\\', '/', substr($class, $namespaceLen)) . '.php';
            if (file_exists($file)) {
                require_once $file;
                static::initializeModel($class);
            }
        });

        // Register an autoload function for the 'Middleware' directory
        spl_autoload_register(function ($class) {
            $namespace = __NAMESPACE__ . '\\Middleware\\';
            $namespaceLen = strlen($namespace);
            if (strcmp(substr($class, 0, $namespaceLen), __NAMESPACE__ . '\\Middleware\\') !== 0)
                return;
            $file = MIDDLEWARE_DIR . str_replace(__NAMESPACE__ . '\\Middleware\\', '/', $class) . '.php';
            if (file_exists($file)) {
                require_once $file;
            }
        });

        // Register an autoload function for the 'Controllers' directory
        spl_autoload_register(function ($class) {
            $namespace = __NAMESPACE__ . '\\Controllers\\';
            $namespaceLen = strlen($namespace);
            if (strcmp(substr($class, 0, $namespaceLen), __NAMESPACE__ . '\\Controllers\\') !== 0)
                return;
            $file = CONTROLLERS_DIR . str_replace(__NAMESPACE__ . '\\Controllers\\', '/', $class) . '.php';
            if (file_exists($file)) {
                require_once $file;
            }
        });

        // Register an autoload function for the 'Services' directory
        spl_autoload_register(function ($class) {
            $namespace = __NAMESPACE__ . '\\Services\\';
            $namespaceLen = strlen($namespace);
            if (strcmp(substr($class, 0, $namespaceLen), __NAMESPACE__ . '\\Services\\') !== 0)
                return;
            $file = SERVICES_DIR . str_replace(__NAMESPACE__ . '\\Services\\', '/', $class) . '.php';
            if (file_exists($file)) {
                require_once $file;
            }
        });

        // Register an autoload function for the 'vendor/facades' directory
        spl_autoload_register(function ($class) {
            $namespace = __NAMESPACE__ . '\\Facades\\';
            $namespaceLen = strlen($namespace);
            if (strcmp(substr($class, 0, $namespaceLen), __NAMESPACE__ . '\\Facades\\') !== 0)
                return;
            $file = FACADES_DIR . str_replace(__NAMESPACE__ . '\\Facades\\', '/', $class) . '.php';
            if (file_exists($file)) {
                require_once $file;
            }
        });

        // Register an autoload function for the 'vendor/base' directory
        spl_autoload_register(function ($class) {
            $class = substr($class, strlen(__NAMESPACE__) + 1);
            $file = BASE_DIR . str_replace('\\', '/', $class) . '.php';
            if (file_exists($file)) {
                require_once $file;
            }
        });
    }

    static public function initializeModel($class) {
        $class::$db = ConnectionSingleton::getInstance()->getConnection();
        $class::$queryBuilder = QueryBuilderSingleton::getInstance()->getQueryBuilder();
        $class::$table = pluralize(camelToSnake(array_slice(explode('\\', $class), -1)[0]));
    }

    static public function initializeApplication()
    {
        // Register the autoload functions
        static::register();

        try {
            ConnectionSingleton::getInstance();
            QueryBuilderSingleton::getInstance();
        } catch (PDOException $e) {
            dd("Connection failed: " . $e->getMessage());
        }

        RouterSingleton::getInstance();
        FileStorageSingleton::getInstance();
    }
}

Autoloader::loadGlobals();
Autoloader::initializeApplication();
