<?php

namespace App;

use App\Interfaces\SingletonInterface;

class Singleton implements SingletonInterface
{
    /**
     * The instance of the singleton class.
     *
     * @var self
     */
    private static $instance;

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
    }

    /**
     * Private unserialize method to prevent unserializing.
     */
    public function __wakeup()
    {
    }

    /**
     * Returns the instance of the singleton class.
     *
     * @return self The instance.
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        if (method_exists(self::$instance,'reset')) {
            call_user_func([self::$instance, 'reset']);
        }

        return self::$instance;
    }
}
