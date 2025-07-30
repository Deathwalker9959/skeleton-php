<?php

namespace App\Router;

class Route
{

    /**
     * Parses a controller method and returns an array of route information.
     *
     * @param string $httpMethod The HTTP method of the route (e.g. GET, POST, PUT, DELETE).
     * @param string $location The location of the route (e.g. /users).
     * @param string $function A string in the format "Controller@method" that specifies the controller class and method to be called for the route.
     *
     * @return array An array of route information.
     */
    private static function parseController(string $httpMethod, string $location, string $function)
    {
        // Split the controller and method by the @ symbol
        $path = explode("@", $function);
        $class = $path[0];
        $method = $path[1];
        $bindings = [];

        // Find all occurrences of two or more sets of curly brackets between slashes in the string
        preg_match_all('/\/({[^\/].[^\/]*?}{[^\/].[^\/]*?}){1,}\//', $location, $dupMatches, PREG_UNMATCHED_AS_NULL);

        if (isset($dupMatches[1]) && $dupMatches[1] != null) {
            dd("Illegal route declaration", "Location: " . $location, "Offending match: " . implode($dupMatches[1]));
        }

        /**
         * Find all occurrences of bindings between curly braces in the string,
         * and store them in the $bindings array.
         */
        preg_match_all('/{(.*?)}/', $location, $matches, PREG_OFFSET_CAPTURE);
        $bindings = array_combine(array_map(function ($token) use ($location) {
            $numSlashes = substr_count($location, '/');
            return $numSlashes - substr_count($location, '/', $token[1]) - 1;
        }, $matches[1]), array_map(function ($token) use ($location) {
            return [
                'location' => $location,
                'token' => $token[0],
                'predicted_model' => static::predictModel($token[0]),
            ];
        }, $matches[1]));


        // Get the path part of the location (e.g. /users from http://example.com/users)
        $location = parse_url($location)['path'];

        // Build the fully qualified class name for the controller
        $controllerClass = "App\\Controllers\\{$class}";
        $httpMethod = strtoupper($httpMethod);

        // Return an array of route information
        return [
            "{$httpMethod}" => [
                [
                    "location" => $location,
                    "controllerClass" => $controllerClass, // The fully qualified class name of the controller
                    "method" => $method, // The method to be called on the controller
                    "bindings" => $bindings,
                ],
            ]
        ];
    }


    private static function countSlashesPastPos($string, $pos)
    {
        // Initialize the count
        $count = 0;

        // Iterate over the characters in the string
        for ($i = $pos; $i < strlen($string); $i++) {
            // Check if the current character is a slash
            if ($string[$i] === '/') {
                // If it is a slash, increment the count
                $count++;
            }
        }

        // Return the count
        return $count;
    }

    /**
     * Predict the model for the given token by searching for a lowercased
     * version of the token in the models directory.
     *
     * @param string $token The token to predict the model for.
     *
     * @return string|null The name of the predicted model, or null if no matching class is found.
     */
    private static function predictModel($token)
    {
        // Navigate to the models directory
        $modelsDir = MODELS_DIR;
        $files = array_diff(scandir($modelsDir), ['.', '..']);

        // Search for a lowercased version of the token
        $lowercasedToken = strtolower($token);
        foreach ($files as $file) {
            if (strtolower($file) === "{$lowercasedToken}.php") {
                // If a matching file is found, return the name of the class
                return "App\\Models\\" . ucfirst($lowercasedToken);
            }
        }

        // If no matching file is found, return null
        return null;
    }

    /**
     * Defines a GET route.
     *
     * @param string $location The location of the route (e.g. /users).
     * @param string $function A string in the format "Controller@method" that specifies the controller class and method to be called for the route.
     *
     * @return array An array of route information.
     */
    public static function Get(string $location, string $function)
    {
        return Route::parseController(__FUNCTION__, $location, $function);
    }

    /**
     * Defines a POST route.
     *
     * @param string $location The location of the route (e.g. /users).
     * @param string $function A string in the format "Controller@method" that specifies the controller class and method to be called for the route.
     *
     * @return array An array of route information.
     */
    public static function Post(string $location, string $function)
    {
        return Route::parseController(__FUNCTION__, $location, $function);
    }
    /**
     * Defines a PUT route.
     *
     * @param string $location The location of the route (e.g. /users).
     * @param string $function A string in the format "Controller@method" that specifies the controller class and method to be called for the route.
     *
     * @return array An array of route information.
     */
    public static function Put(string $location, string $function)
    {
        return Route::parseController(__FUNCTION__, $location, $function);
    }

    /**
     * Defines a PATCH route.
     *
     * @param string $location The location of the route (e.g. /users).
     * @param string $function A string in the format "Controller@method" that specifies the controller class and method to be called for the route.
     *
     * @return array An array of route information.
     */
    public static function Patch(string $location, string $function)
    {
        return Route::parseController(__FUNCTION__, $location, $function);
    }

    /**
     * Defines a DELETE route.
     *
     * @param string $location The location of the route (e.g. /users).
     * @param string $function A string in the format "Controller@method" that specifies the controller class and method to be called for the route.
     *
     * @return array An array of route information.
     */
    public static function Delete(string $location, string $function)
    {
        return Route::parseController(__FUNCTION__, $location, $function);
    }

    /**
     * Defines a group of routes with a shared prefix and/or middleware.
     *
     * @param array $options An array of options for the group.
     * @param array $routes An array of routes in the group.
     *
     * @return array An array of route information for the group.
     */
    public static function Group($options, $routes)
    {
        // Get the prefix and middleware options, or use null if they are not set
        $prefix = $options['prefix'] ?? null;
        $middleware = $options['middleware'] ?? null;

        // Flatten the array of routes
        $flattenedRoutes = call_user_func_array('array_merge_recursive', $routes);

        // Return an array of route information for the group
        return [
            "id" => random_bytes(10), // Generate a random ID for the group
            "type" => "group",
            "prefix" => $prefix, // The prefix for the group
            "middleware" => $middleware, // The middleware for the group
            "routes" => $flattenedRoutes // The routes in the group
        ];
    }
}
