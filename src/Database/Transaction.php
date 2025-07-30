<?php

/**
 * Part of the Skeleton framework.
 */

namespace Skeleton\Database;

use Skeleton\ConnectionSingleton;
use PDO;

class Transaction
{
    /**
     * Whether a transaction is currently active.
     */
    private bool $active = false;

    /**
     * Create a new transaction instance.
     *
     * @param PDO $pdo The PDO instance to use for the transaction.
     */
    public function __construct(
        /**
         * The PDO instance.
         */
        private readonly PDO $pdo
    )
    {
    }

    /**
     * Begin a transaction.
     *
     * @return bool Whether the transaction was successfully started.
     */
    public function begin(): bool
    {
        if ($this->active) {
            return false;
        }

        $this->active = $this->pdo->beginTransaction();

        return $this->active;
    }

    /**
     * Commit a transaction.
     *
     * @return bool Whether the transaction was successfully committed.
     */
    public function commit(): bool
    {
        if (!$this->active) {
            return false;
        }

        $this->active = !$this->pdo->commit();

        return !$this->active;
    }

    /**
     * Roll back a transaction.
     *
     * @return bool Whether the transaction was successfully rolled back.
     */
    public function rollBack(): bool
    {
        if (!$this->active) {
            return false;
        }

        $this->active = !$this->pdo->rollBack();

        return !$this->active;
    }
}
