<?php

namespace App\Interfaces;

interface SingletonInterface
{
    /**
     * Returns the instance of the singleton class.
     *
     * @return static The instance.
     */
    public static function getInstance();

    /**
     * Private clone method to prevent cloning.
     */
    public function __clone();

    /**
     * Private unserialize method to prevent unserializing.
     */
    public function __wakeup();
}
