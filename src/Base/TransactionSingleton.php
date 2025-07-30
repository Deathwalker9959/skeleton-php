<?php
/**
 * Part of the Skeleton framework.
 */

namespace Skeleton;

use Skeleton\Database\Transaction;
use Skeleton\Singleton;
use Skeleton\Router\Request;

class TransactionSingleton extends Singleton
{

    /**
     * The instance of the singleton class.
     *
     * @var TransactionSingleton
     */
    private static $instance;


    /**
     * The PDO connection object.
     *
     * @var Transaction
     */
    private $transaction;

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
     * @return Transaction The connection object.
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}
