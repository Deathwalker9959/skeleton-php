<?php

namespace App;

use App\Singleton;
use App\Router\Request;

class RequestSingleton extends Singleton
{

    /**
     * The instance of the singleton class.
     *
     * @var RequestSingleton
     */
    private static $instance;


    /**
     * The PDO connection object.
     *
     * @var Request
     */
    private $request;

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
     * @return Request The connection object.
     */
    public function getRequest()
    {
        return $this->request;
    }
}
