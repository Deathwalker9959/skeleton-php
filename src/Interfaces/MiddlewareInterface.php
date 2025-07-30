<?php

/**
 * Part of the Skeleton framework.
 */

namespace Skeleton\Interfaces;

use Skeleton\Router\Request;

/**
 * Interface Middleware
 *
 * A middleware interface that defines a method for handling a request.
 */
interface MiddlewareInterface
{
    /**
     * Handle a request.
     *
     * @param Request $request The request to be handled.
     * @param array $models The models to be processed.
     *
     * @return void
     */
    public function handle(Request $request, array $models): void;
}
