<?php

/**
 * Part of the Skeleton framework.
 */

namespace Skeleton\Router;

class Request
{
    /**
     * The HTTP method of the request (e.g. GET, POST, PUT, DELETE).
     *
     * @var string
     */
    public $method;

    /**
     * The URI of the request.
     *
     * @var string
     */
    public $uri;

    /**
     * An array of request headers.
     *
     * @var array
     */
    public $headers;

    /**
     * The request body.
     *
     * @var string
     */
    public $body;

    /**
     * An array of query string parameters.
     *
     * @var array
     */
    public $query;

    /**
     * An array of POST parameters.
     *
     * @var array
     */
    public $post;

    /**
     * An array of uploaded files.
     *
     * @var array
     */
    public $files;

    /**
     * Construct a new Request object by parsing the current PHP request.
     */
    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->uri = $_SERVER['REQUEST_URI'] ?? '/';
        $this->headers = function_exists('getallheaders') ? getallheaders() : $this->getAllHeadersManual();
        $this->body = file_get_contents('php://input');
        $this->query = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
    }

    /**
     * Manual implementation of getallheaders for environments where it's not available
     */
    private function getAllHeadersManual(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $headers[str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))))] = $value;
            }
        }

        return $headers;
    }

    /**
     * Retrieve the value of an input parameter.
     *
     * @param string $key The key of the input parameter to retrieve.
     * @param mixed $default The default value to use if the parameter does not exist.
     * @return mixed The value of the input parameter, or the default value if it does not exist.
     */
    public function input($key, mixed $default = null)
    {
        if (isset($_REQUEST[$key])) {
            $value = $_REQUEST[$key];
            if (is_array($value)) {
                return $value;
            }

            return trim((string) $value);
        }

        $jsonData = json_decode($this->body, true);
        return $jsonData[$key] ?? $default;
    }

    /**
     * Retrieve the value of an uploaded file input.
     *
     * @param string $key The key of the uploaded file input to retrieve.
     * @param mixed $default The default value to use if the input does not exist.
     * @return mixed The value of the uploaded file input, or the default value if it does not exist.
     */
    public function file($key, mixed $default = null)
    {
        return $this->files[$key] ?? $default;
    }


    /**
     * Check if the request method is equal to the given method.
     *
     * @param string $method The method to check against (e.g. GET, POST, PUT, DELETE).
     * @return bool True if the request method is equal to the given method, false otherwise.
     */
    public function isMethod($method): bool
    {
        return $this->method === strtoupper($method);
    }
}
