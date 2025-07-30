<?php

namespace App\Database;

use App\ConnectionSingleton;
use PDO;

class Transaction
{
    /**
     * The PDO instance.
     *
     * @var PDO
     */
    private PDO $pdo;

    /**
     * Whether a transaction is currently active.
     *
     * @var bool
     */
    private bool $active = false;

    /**
     * Create a new transaction instance.
     *
     * @param PDO $pdo The PDO instance to use for the transaction.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
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
