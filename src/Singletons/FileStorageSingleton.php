<?php

/**
 * Part of the Skeleton framework.
 */

namespace Skeleton\Singletons;

use Skeleton\Singleton;
use Skeleton\FileStorage;

class FileStorageSingleton extends Singleton
{
    /**
     * The instance of the singleton class.
     */
    private static ?\Skeleton\Singletons\FileStorageSingleton $instance = null;


    /**
     * The PDO connection object.
     */
    private readonly FileStorage $fileStorage;

    /**
     * Private constructor to prevent direct instantiation.
     */
    private function __construct()
    {
        $this->fileStorage = new FileStorage(STORAGE_PATH);
    }

    /**
     * Returns the instance of the singleton class.
     *
     * @return FileStorageSingleton The instance.
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
     * @return FileStorage The connection object.
     */
    public function getFileStorage(): FileStorage
    {
        return $this->fileStorage;
    }
}
