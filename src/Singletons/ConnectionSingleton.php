<?php

/**
 * Part of the Skeleton framework.
 */

namespace Skeleton\Singletons;

use Exception;
use PDO;
use Skeleton\Singleton;

class ConnectionSingleton extends Singleton
{
    /**
     * The instance of the singleton class.
     */
    private static ?\Skeleton\Singletons\ConnectionSingleton $instance = null;

    /**
     * The PDO connection object.
     */
    private readonly PDO $conn;

    /**
     * Private constructor to prevent direct instantiation.
     */
    private function __construct()
    {
        global $APP_CONFIG;
        $dbConfig = $APP_CONFIG['db'] ?? [];
        
        // Check if PDO extension is available
        if (!extension_loaded('pdo')) {
            throw new Exception('PDO extension is not loaded');
        }
        
        $driver = $dbConfig['driver'] ?? 'mysql';
        $host = $dbConfig['host'] ?? 'localhost';
        $port = $dbConfig['port'] ?? '3306';
        $database = $dbConfig['database'] ?? 'test';
        $username = $dbConfig['username'] ?? 'root';
        $password = $dbConfig['password'] ?? '';
        $charset = $dbConfig['charset'] ?? 'utf8mb4';
        
        // Check if the specific PDO driver is available
        $availableDrivers = PDO::getAvailableDrivers();
        if (!in_array($driver, $availableDrivers)) {
            throw new Exception(sprintf("PDO driver '%s' is not available. Available drivers: ", $driver) . implode(', ', $availableDrivers));
        }
        
        // Connect to the database
        $this->conn = new PDO(sprintf('%s:host=%s;port=%s;dbname=%s;charset=%s', $driver, $host, $port, $database, $charset), $username, $password);
        // set the PDO error mode to exception
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    }

    /**
     * Returns the instance of the singleton class.
     *
     * @return ConnectionSingleton The instance.
     */
    public static function getInstance(): static
    {
        if (!isset(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Returns the PDO connection object.
     *
     * @return PDO The connection object.
     */
    public function getConnection(): PDO
    {
        return $this->conn;
    }
}
