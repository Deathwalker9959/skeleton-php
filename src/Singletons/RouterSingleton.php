<?php

/**
 * Part of the Skeleton framework.
 */

namespace Skeleton\Singletons;

use Skeleton\Router;
use Skeleton\Singleton;

class RouterSingleton extends Singleton
{
    /**
     * The instance of the singleton class.
     */
    private static ?\Skeleton\Singletons\RouterSingleton $instance = null;

    /**
     * The PDO connection object.
     */
    private readonly Router $router;

    /**
     * Private constructor to prevent direct instantiation.
     */
    private function __construct()
    {
        $this->router = new Router();
    }

    /**
     * Returns the instance of the singleton class.
     *
     * @return RouterSingleton The instance.
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
     * @return Router The connection object.
     */
    public function getRouter(): Router
    {
        return $this->router;
    }
}
