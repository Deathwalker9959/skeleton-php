<?php
/**
 * Part of the Skeleton framework.
 */

namespace Skeleton\Router;

use Skeleton\Router\Response;

class MiddlewareResponse
{

    private Response | null $response;
    private bool $result;

    public function __construct($result, $response = null)
    {
        $this->result = $result;
        $this->response = $response;
    }
    public function getResponse()
    {
        return $this->response ?? null;
    }

    public function getResult()
    {
        return $this->result ?? false;
    }
}
