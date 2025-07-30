<?php

/**
 * Part of the Skeleton framework.
 */

namespace Skeleton\Singletons;

use Skeleton\Singleton;
use Skeleton\Router\Request;

class RequestSingleton extends Singleton
{
    /**
     * The instance of the singleton class.
     */
    private static ?\Skeleton\Singletons\RequestSingleton $instance = null;


    /**
     * The PDO connection object.
     */
    private readonly Request $request;

    /**
     * Private constructor to prevent direct instantiation.
     */
    private function __construct()
    {
        $this->request = new Request();
    }

    /**
     * Returns the instance of the singleton class.
     *
     * @return RequestSingleton The instance.
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
     * @return Request The connection object.
     */
    public function getRequest(): Request
    {
        return $this->request;
    }
}
