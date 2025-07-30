<?php

namespace App;

use App\Router;

class RouterSingleton extends Singleton
{

    /**
     * The instance of the singleton class.
     *
     * @var ConnectionSingleton
     */
    private static $instance;

    /**
     * The PDO connection object.
     *
     * @var Router
     */
    private $router;

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
     * @return ConnectionSingleton The instance.
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
     * @return Router The connection object.
     */
    public function getRouter()
    {
        return $this->router;
    }
}
