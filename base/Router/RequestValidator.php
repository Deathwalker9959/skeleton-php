<?php

namespace App\Router;

class RequestValidator
{
    /**
     * Validate a Request object.
     *
     * @param Request $request The Request object to validate.
     * @return bool True if the request is valid, false otherwise.
     */
    public static function validate(Request $request)
    {
        // Validate the request method
        if (!in_array($request->method, ['GET', 'POST', 'PUT', 'DELETE'])) {
            return false;
        }

        // Validate the URI
        if (!filter_var($request->uri, FILTER_VALIDATE_URL)) {
            return false;
        }

        // Validate the headers
        if (!is_array($request->headers)) {
            return false;
        }

        // Validate the body
        if (!is_string($request->body)) {
            return false;
        }

        // Validate the query parameters
        if (!is_array($request->query)) {
            return false;
        }

        // Validate the POST parameters
        if (!is_array($request->post)) {
            return false;
        }

        // Validate the uploaded files
        if (!is_array($request->files)) {
            return false;
        }

        return true;
    }

    /**
     * Validate an input parameter.
     *
     * @param Request $request The Request object containing the input parameter.
     * @param string $key The key of the input parameter to validate.
     * @param mixed $default The default value to use if the parameter does not exist.
     * @return bool True if the input parameter is valid, false otherwise.
     */
    public static function validateInput(Request $request, $key, $default = null)
    {
        $value = $request->input($key, $default);

        if (empty($value) || !is_string($value)) {
            return false;
        }

        return true;
    }

    /**
     * Validate an array of input keys.
     *
     * @param Request $request The Request object containing the input keys.
     * @param array $keys The keys of the input parameters to validate.
     * @return bool True if all input parameters are valid, false otherwise.
     */
    public static function validateInputKeys(Request $request, array $keys)
    {
        foreach ($keys as $key) {
            if (!self::validateInput($request, $key)) {
                return false;
            }
        }

        return true;
    }
}
