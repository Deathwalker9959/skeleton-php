<?php

namespace App\Router;

class Response
{
    private $statusCode = 200;
    private $headers = [];
    private $body;

    public function __construct()
    {
    }

    /**
     * Set the status code of the response.
     *
     * @param int $statusCode The status code to set.
     * @return Response The current response object, for chaining.
     */
    public function status(int $statusCode): Response
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * Add a header to the response.
     *
     * @param string $name The name of the header.
     * @param string $value The value of the header.
     * @return Response The current response object, for chaining.
     */
    public function header(string $name, string $value): Response
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Set the body of the response.
     *
     * @param mixed $body The body of the response.
     * @return Response The current response object, for chaining.
     */
    public function body($body): Response
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Send the response.
     *
     * This method sends the status code, headers, and body of the response to the client.
     */
    public function send(): bool
    {
        // Set the status code
        http_response_code($this->statusCode);

        // Set the headers
        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        // Send the body
        echo $this->body;

        return true;
    }

    /**
     * Render a view file.
     *
     * This method renders a view file and returns the contents as a string.
     *
     * @param string $view The name of the view file to render.
     * @param array $data An array of data to pass to the view.
     * @return Response The current response object, for chaining.
     */
    public function view(string $view, array $data = []): Response
    {
        // Extract the data into variables
        extract($data, EXTR_SKIP);

        $view = str_replace('.', '/', $view);

        // Start an output buffer
        ob_start();
        // Include the view file
        include VIEWS_DIR . "{$view}.php";

        // Get the contents of the output buffer
        $contents = ob_get_clean();

        ob_start();

        include ASSETS_DIR . "bootstrap.php";

        $bootstrap = ob_get_clean();

        // Set the body of the response
        $this->body = $bootstrap . $contents;

        // Return the current response object, for chaining
        return $this;
    }

    /**
     * Redirect the user to a different URL.
     *
     * @param string $url The URL to redirect the user to.
     * @param int $statusCode The HTTP status code to use for the redirect.
     * @return bool Whether the redirect was successful.
     */
    public function redirect(string $url, int $statusCode = 302): Response
    {
        // Set the status code and location header
        $this->status($statusCode);
        $this->header('Location', $url);

        return $this;
    }

    /**
     * Set the content type of the response.
     *
     * @param string $type The content type of the response.
     * @return Response The current response object, for chaining.
     */
    public function type(string $type): Response
    {
        $this->header('Content-Type', $type);
        return $this;
    }

    /**
     * Set the cache control header for the response.
     *
     * @param string $cacheControl The cache control directive to set.
     * @return Response The current response object, for chaining.
     */
    public function cacheControl(string $cacheControl): Response
    {
        $this->header('Cache-Control', $cacheControl);
        return $this;
    }

    /**
     * Set the body of the response as a JSON string.
     *
     * @param mixed $data The data to encode as JSON.
     * @param int $options Options for json_encode.
     * @param int $statusCode The HTTP status code to set for the response.
     * @return Response The current response object, for chaining.
     */
    public function json($data, $options = 0, $statusCode = 200): Response
    {
        // Set the status code and content type header
        $this->status($statusCode);
        $this->header('Content-Type', 'application/json');

        // Encode the data as a JSON string
        $json = json_encode($data, $options);

        // Set the body of the response
        $this->body($json);

        return $this;
    }
}
