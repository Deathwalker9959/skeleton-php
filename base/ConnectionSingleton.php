<?php

namespace App;

use PDO;
use App\Singleton;

class ConnectionSingleton extends Singleton
{

    /**
     * The instance of the singleton class.
     *
     * @var ConnectionSingleton
     */
    private static $instance;

    /**
     * The PDO connection object.
     *
     * @var PDO
     */
    private $conn;

    /**
     * Private constructor to prevent direct instantiation.
     */
    private function __construct()
    {
        $dbConfig = APP_CONFIG['db'];
        $driver = $dbConfig['driver'];
        $host = $dbConfig['host'];
        $port = $dbConfig['port'];
        $database = $dbConfig['database'];
        $username = $dbConfig['username'];
        $password = $dbConfig['password'];
        $charset = $dbConfig['charset'];
        // Connect to the database
        $this->conn = new PDO("{$driver}:host={$host};port={$port};dbname={$database};charset={$charset}", $username, $password);
        // set the PDO error mode to exception
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    }

    /**
     * Returns the instance of the singleton class.
     *
     * @return ConnectionSingleton The instance.
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Returns the PDO connection object.
     *
     * @return PDO The connection object.
     */
    public function getConnection()
    {
        return $this->conn;
    }
}
