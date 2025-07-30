<?php

/**
 * Part of the Skeleton framework.
 */

namespace Skeleton;

use Exception;
use Skeleton\Interfaces\SingletonInterface;

class Singleton implements SingletonInterface
{
    /**
     * The instance of the singleton class.
     */
    private static ?\Skeleton\Singleton $instance = null;

    /**
     * Private constructor to prevent direct instantiation.
     */
    private function __construct()
    {
    }

    /**
     * Private clone method to prevent cloning.
     */
    public function __clone()
    {
        throw new Exception('Cannot clone a singleton.');
    }

    /**
     * Private unserialize method to prevent unserializing.
     */
    public function __wakeup()
    {
        throw new Exception('Cannot unserialize a singleton.');
    }

    /**
     * Returns the instance of the singleton class.
     *
     * @return static The instance.
     */
    public static function getInstance(): static
    {
        if (!isset(static::$instance)) {
            static::$instance = new static();
        }

        if (method_exists(static::$instance, 'reset')) {
            call_user_func([static::$instance, 'reset']);
        }

        return static::$instance;
    }
}
