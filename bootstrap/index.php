<?php

/**
 * Skeleton Framework Bootstrap File
 * 
 * This file is the entry point for all HTTP requests to your application.
 * It loads the necessary files and starts the application.
 */

declare(strict_types=1);

use Skeleton\Singletons\RouterSingleton;
use Skeleton\Router\Request;
use Skeleton\Router\Response;
use Skeleton\HttpStatusCodes;

// Load environment variables if .env file exists
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue; // Skip comments
        }
        
        if (strpos($line, '=') !== false) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
}

// Set error reporting based on environment
if (($_ENV['APP_DEBUG'] ?? false) && ($_ENV['APP_ENV'] ?? 'production') !== 'production') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ERROR | E_PARSE);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}

// Set default timezone
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'UTC');

try {
    // Load Composer autoloader and application bootstrap
    require_once __DIR__ . '/vendor/autoload.php';
    require_once __DIR__ . '/autoload.php';

    // Create request object from globals
    $request = new Request();
    
    // Get router instance
    $router = RouterSingleton::getInstance()->getRouter();
    
    // Basic routing setup - this would typically be done in routes files
    // For now, we'll just create a basic response
    $response = new Response();
    $response->header('Content-Type', 'application/json');
    $response->json([
        'message' => 'Welcome to Skeleton Framework',
        'status' => 'success',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
    // Send the response
    $response->send();

} catch (Throwable $e) {
    // Log the error
    error_log("Application Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    
    // Send appropriate error response
    if (($_ENV['APP_DEBUG'] ?? false) && ($_ENV['APP_ENV'] ?? 'production') !== 'production') {
        // Development: Show detailed error
        $response = new Response();
        $response->status(HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
        $response->header('Content-Type', 'application/json');
        $response->json([
            'error' => 'Internal Server Error',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);
        $response->send();
    } else {
        // Production: Show generic error
        http_response_code(HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => 'Internal Server Error',
            'message' => 'Something went wrong. Please try again later.'
        ]);
    }
    
    exit(1);
}
