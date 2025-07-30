<?php

declare(strict_types=1);

/**
 * Configuration file for Skeleton Framework
 * 
 * This file defines the constants used throughout the framework.
 */

// Base paths for the application
if (!defined('MODELS_DIR')) {
    define('MODELS_DIR', __DIR__ . '/src/Models/');
}

if (!defined('ROUTES_DIR')) {
    define('ROUTES_DIR', __DIR__ . '/routes/');
}

if (!defined('VIEWS_DIR')) {
    define('VIEWS_DIR', __DIR__ . '/views/');
}

if (!defined('ASSETS_DIR')) {
    define('ASSETS_DIR', __DIR__ . '/assets/');
}

if (!defined('STORAGE_PATH')) {
    define('STORAGE_PATH', __DIR__ . '/storage/');
}

// Create directories if they don't exist
$directories = [
    ROUTES_DIR,
    VIEWS_DIR,
    ASSETS_DIR,
    STORAGE_PATH,
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}
