<?php

namespace App\Interfaces;

use App\Router\Request;


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
     */
    public function handle(Request $request, $models);
}
