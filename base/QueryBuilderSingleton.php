<?php

namespace App;

use App\Singleton;
use App\Database\QueryBuilder;

class QueryBuilderSingleton extends Singleton
{

    /**
     * The instance of the singleton class.
     *
     * @var QueryBuilderSingleton
     */
    private static $instance;

    /**
     * The PDO connection object.
     *
     * @var PDO
     */
    private $conn;

    /**
     * The PDO connection object.
     *
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * Private constructor to prevent direct instantiation.
     */
    private function __construct()
    {
        $this->conn = ConnectionSingleton::getInstance()->getConnection();
        $this->queryBuilder = new QueryBuilder($this->conn);
    }

    /**
     * Returns the instance of the singleton class.
     *
     * @return QueryBuilderSingleton The instance.
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
     * @return QueryBuilder The connection object.
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }
}
