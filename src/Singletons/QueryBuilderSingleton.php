<?php

/**
 * Part of the Skeleton framework.
 */

namespace Skeleton\Singletons;

use PDO;
use Skeleton\Singleton;
use Skeleton\Database\QueryBuilder;

class QueryBuilderSingleton extends Singleton
{
    /**
     * The instance of the singleton class.
     */
    private static ?\Skeleton\Singletons\QueryBuilderSingleton $instance = null;

    /**
     * The PDO connection object.
     *
     * @var PDO
     */
    private $conn;

    /**
     * The PDO connection object.
     */
    private readonly QueryBuilder $queryBuilder;

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
     * @return QueryBuilder The connection object.
     */
    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }
}
