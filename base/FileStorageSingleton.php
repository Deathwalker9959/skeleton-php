<?php

namespace App;

use App\Singleton;
use App\FileStorage;

class FileStorageSingleton extends Singleton
{

    /**
     * The instance of the singleton class.
     *
     * @var FileStorageSingleton
     */
    private static $instance;


    /**
     * The PDO connection object.
     *
     * @var FileStorage
     */
    private $fileStorage;

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
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self(STORAGE_PATH);
        }

        return self::$instance;
    }

    /**
     * Returns the PDO connection object.
     *
     * @return FileStorage The connection object.
     */
    public function getFileStorage()
    {
        return $this->fileStorage;
    }
}
