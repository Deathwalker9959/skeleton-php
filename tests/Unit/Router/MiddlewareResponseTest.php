<?php

namespace Skeleton\Tests\Unit\Router;

use Skeleton\Tests\BaseTestCase;
use Skeleton\Router\MiddlewareResponse;
use Skeleton\Router\Response;

class MiddlewareResponseTest extends BaseTestCase
{
    /**
     * Test constructor with result only
     */
    public function testConstructorWithResultOnly(): void
    {
        $middlewareResponse = new MiddlewareResponse(true);
        
        $this->assertTrue($middlewareResponse->getResult());
        $this->assertNull($middlewareResponse->getResponse());
    }

    /**
     * Test constructor with result and response
     */
    public function testConstructorWithResultAndResponse(): void
    {
        $response = new Response();
        $response->body('Test content');
        
        $middlewareResponse = new MiddlewareResponse(true, $response);
        
        $this->assertTrue($middlewareResponse->getResult());
        $this->assertSame($response, $middlewareResponse->getResponse());
    }

    /**
     * Test constructor with false result
     */
    public function testConstructorWithFalseResult(): void
    {
        $middlewareResponse = new MiddlewareResponse(false);
        
        $this->assertFalse($middlewareResponse->getResult());
        $this->assertNull($middlewareResponse->getResponse());
    }

    /**
     * Test constructor with false result and response
     */
    public function testConstructorWithFalseResultAndResponse(): void
    {
        $response = new Response();
        $response->status(403)->body('Forbidden');
        
        $middlewareResponse = new MiddlewareResponse(false, $response);
        
        $this->assertFalse($middlewareResponse->getResult());
        $this->assertSame($response, $middlewareResponse->getResponse());
    }

    /**
     * Test getResult method
     */
    public function testGetResult(): void
    {
        $successResponse = new MiddlewareResponse(true);
        $this->assertTrue($successResponse->getResult());
        
        $failureResponse = new MiddlewareResponse(false);
        $this->assertFalse($failureResponse->getResult());
    }

    /**
     * Test getResponse method with null response
     */
    public function testGetResponseWithNull(): void
    {
        $middlewareResponse = new MiddlewareResponse(true);
        
        $response = $middlewareResponse->getResponse();
        $this->assertNull($response);
    }

    /**
     * Test getResponse method with valid response
     */
    public function testGetResponseWithValidResponse(): void
    {
        $response = new Response();
        $response->status(200)->body('Success');
        
        $middlewareResponse = new MiddlewareResponse(true, $response);
        
        $retrievedResponse = $middlewareResponse->getResponse();
        $this->assertSame($response, $retrievedResponse);
        $this->assertInstanceOf(Response::class, $retrievedResponse);
    }

    /**
     * Test middleware response for authentication success
     */
    public function testAuthenticationSuccess(): void
    {
        $middlewareResponse = new MiddlewareResponse(true);

        $this->assertTrue($middlewareResponse->getResult());
        $this->assertNull($middlewareResponse->getResponse());

        // In real middleware, a successful authentication would allow the request to continue
        // and no response would be set
    }

    /**
     * Test middleware response for authentication failure
     */
    public function testAuthenticationFailure(): void
    {
        $response = new Response();
        $response->status(401)
                ->header('Content-Type', 'application/json')
                ->json(['error' => 'Unauthorized', 'message' => 'Invalid credentials']);
        
        $middlewareResponse = new MiddlewareResponse(false, $response);
        
        $this->assertFalse($middlewareResponse->getResult());
        $this->assertSame($response, $middlewareResponse->getResponse());
    }

    /**
     * Test middleware response for rate limiting
     */
    public function testRateLimitingFailure(): void
    {
        $response = new Response();
        $response->status(429)
                ->header('X-RateLimit-Limit', '100')
                ->header('X-RateLimit-Remaining', '0')
                ->header('Retry-After', '3600')
                ->json(['error' => 'Too Many Requests', 'message' => 'Rate limit exceeded']);
        
        $middlewareResponse = new MiddlewareResponse(false, $response);
        
        $this->assertFalse($middlewareResponse->getResult());
        $this->assertInstanceOf(Response::class, $middlewareResponse->getResponse());
    }

    /**
     * Test middleware response for CORS preflight
     */
    public function testCorsPreflightResponse(): void
    {
        $response = new Response();
        $response->status(200)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                ->body('');
        
        $middlewareResponse = new MiddlewareResponse(true, $response);
        
        $this->assertTrue($middlewareResponse->getResult());
        $this->assertSame($response, $middlewareResponse->getResponse());
    }

    /**
     * Test middleware response for validation failure
     */
    public function testValidationFailure(): void
    {
        $response = new Response();
        $response->status(422)
                ->header('Content-Type', 'application/json')
                ->json([
                    'error' => 'Validation Failed',
                    'errors' => [
                        'email' => ['The email field is required.'],
                        'password' => ['The password must be at least 8 characters.']
                    ]
                ]);
        
        $middlewareResponse = new MiddlewareResponse(false, $response);
        
        $this->assertFalse($middlewareResponse->getResult());
        $this->assertInstanceOf(Response::class, $middlewareResponse->getResponse());
    }

    /**
     * Test middleware response immutability
     */
    public function testMiddlewareResponseImmutability(): void
    {
        $response = new Response();
        $response->body('Original content');
        
        $middlewareResponse = new MiddlewareResponse(true, $response);
        
        // Verify the response is returned correctly
        $retrievedResponse = $middlewareResponse->getResponse();
        $this->assertSame($response, $retrievedResponse);
        
        // Verify that modifying the original response doesn't affect the middleware response
        $response->body('Modified content');
        $retrievedResponseAgain = $middlewareResponse->getResponse();
        $this->assertSame($response, $retrievedResponseAgain);
    }

    /**
     * Test multiple middleware responses
     */
    public function testMultipleMiddlewareResponses(): void
    {
        // Simulate authentication middleware
        $authResponse = new MiddlewareResponse(true);
        $this->assertTrue($authResponse->getResult());
        
        // Simulate rate limiting middleware
        $rateLimitResponse = new MiddlewareResponse(true);
        $this->assertTrue($rateLimitResponse->getResult());
        
        // Simulate validation middleware that fails
        $validationFailureResponse = new Response();
        $validationFailureResponse->status(400)->json(['error' => 'Bad Request']);
        $validationResponse = new MiddlewareResponse(false, $validationFailureResponse);
        $this->assertFalse($validationResponse->getResult());
        $this->assertInstanceOf(Response::class, $validationResponse->getResponse());
    }
}
