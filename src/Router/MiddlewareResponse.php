<?php

/**
 * Part of the Skeleton framework.
 */

namespace Skeleton\Router;

use Skeleton\Router\Response;

class MiddlewareResponse
{
    public function __construct(private readonly bool $result, private readonly Response | null $response = null)
    {
    }

    public function getResponse(): ?Response
    {
        return $this->response ?? null;
    }

    public function getResult()
    {
        return $this->result ?? false;
    }
}
