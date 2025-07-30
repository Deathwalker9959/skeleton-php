<?php

/**
 * Part of the Skeleton framework.
 */

namespace Skeleton\Singletons;

use Skeleton\Database\Transaction;
use Skeleton\Singleton;
use Skeleton\Router\Request;

class TransactionSingleton extends Singleton
{
    /**
     * The instance of the singleton class.
     */
    private static ?\Skeleton\Singletons\TransactionSingleton $instance = null;


    /**
     * The PDO connection object.
     */
    private readonly Transaction $transaction;

    /**
     * Private constructor to prevent direct instantiation.
     */
    private function __construct()
    {
        $this->transaction = new Transaction(ConnectionSingleton::getInstance()->getConnection());
    }

    /**
     * Returns the instance of the singleton class.
     *
     * @return TransactionSingleton The instance.
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
     * @return Transaction The connection object.
     */
    public function getTransaction(): Transaction
    {
        return $this->transaction;
    }
}
