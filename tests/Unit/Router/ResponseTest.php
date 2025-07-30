<?php

namespace Skeleton\Tests\Unit\Router;

use stdClass;
use Skeleton\Tests\BaseTestCase;
use Skeleton\Router\Response;

class ResponseTest extends BaseTestCase
{
    private Response $response;

    protected function setUp(): void
    {
        parent::setUp();
        $this->response = new Response();
    }

    /**
     * Test response constructor
     */
    public function testResponseConstructor(): void
    {
        $response = new Response();
        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * Test status method
     */
    public function testStatusMethod(): void
    {
        $result = $this->response->status(404);

        $this->assertInstanceOf(Response::class, $result);
        $this->assertSame($this->response, $result); // Test fluent interface
    }

    /**
     * Test header method
     */
    public function testHeaderMethod(): void
    {
        $result = $this->response->header('Content-Type', 'application/json');

        $this->assertInstanceOf(Response::class, $result);
        $this->assertSame($this->response, $result); // Test fluent interface
    }

    /**
     * Test body method with string
     */
    public function testBodyMethodWithString(): void
    {
        $result = $this->response->body('Hello, World!');

        $this->assertInstanceOf(Response::class, $result);
        $this->assertSame($this->response, $result); // Test fluent interface
    }

    /**
     * Test body method with array
     */
    public function testBodyMethodWithArray(): void
    {
        $data = ['message' => 'Success', 'data' => ['id' => 1, 'name' => 'Test']];
        $result = $this->response->body($data);

        $this->assertInstanceOf(Response::class, $result);
        $this->assertSame($this->response, $result); // Test fluent interface
    }

    /**
     * Test json method with default parameters
     */
    public function testJsonMethodWithDefaults(): void
    {
        $data = ['message' => 'Success', 'status' => 'ok'];
        $result = $this->response->json($data);

        $this->assertInstanceOf(Response::class, $result);
        $this->assertSame($this->response, $result); // Test fluent interface
    }

    /**
     * Test json method with custom status code
     */
    public function testJsonMethodWithCustomStatus(): void
    {
        $data = ['error' => 'Not found'];
        $result = $this->response->json($data, 0, 404);

        $this->assertInstanceOf(Response::class, $result);
        $this->assertSame($this->response, $result); // Test fluent interface
    }

    /**
     * Test json method with custom options
     */
    public function testJsonMethodWithCustomOptions(): void
    {
        $data = ['data' => ['key' => 'value']];
        $result = $this->response->json($data, JSON_PRETTY_PRINT, 201);

        $this->assertInstanceOf(Response::class, $result);
        $this->assertSame($this->response, $result); // Test fluent interface
    }

    /**
     * Test method chaining
     */
    public function testMethodChaining(): void
    {
        $result = $this->response
            ->status(201)
            ->header('Content-Type', 'application/json')
            ->header('X-Custom-Header', 'custom-value')
            ->body(['message' => 'Created successfully']);

        $this->assertInstanceOf(Response::class, $result);
        $this->assertSame($this->response, $result); // Test fluent interface
    }

    /**
     * Test json response method chaining
     */
    public function testJsonResponseChaining(): void
    {
        $result = $this->response
            ->status(200)
            ->header('X-API-Version', '1.0')
            ->json(['data' => 'test'], JSON_PRETTY_PRINT, 200);

        $this->assertInstanceOf(Response::class, $result);
        $this->assertSame($this->response, $result); // Test fluent interface
    }

    /**
     * Test response with various status codes
     */
    public function testVariousStatusCodes(): void
    {
        // Test success codes
        $this->response->status(200);
        $this->response->status(201);
        $this->response->status(204);
        
        // Test client error codes
        $this->response->status(400);
        $this->response->status(401);
        $this->response->status(404);
        
        // Test server error codes
        $this->response->status(500);
        $this->response->status(503);
        
        $this->assertInstanceOf(Response::class, $this->response);
    }

    /**
     * Test response with common headers
     */
    public function testCommonHeaders(): void
    {
        $this->response
            ->header('Content-Type', 'application/json')
            ->header('Cache-Control', 'no-cache')
            ->header('X-RateLimit-Limit', '1000')
            ->header('X-RateLimit-Remaining', '999')
            ->header('Access-Control-Allow-Origin', '*');
        
        $this->assertInstanceOf(Response::class, $this->response);
    }

    /**
     * Test response body with different data types
     */
    public function testBodyWithDifferentTypes(): void
    {
        // String body
        $this->response->body('Plain text response');
        $this->assertInstanceOf(Response::class, $this->response);
        
        // Array body
        $this->response->body(['key' => 'value', 'array' => [1, 2, 3]]);
        $this->assertInstanceOf(Response::class, $this->response);
        
        // Object body
        $obj = new stdClass();
        $obj->property = 'value';
        $this->response->body($obj);
        $this->assertInstanceOf(Response::class, $this->response);
        
        // Numeric body
        $this->response->body(12345);
        $this->assertInstanceOf(Response::class, $this->response);
        
        // Boolean body
        $this->response->body(true);
        $this->assertInstanceOf(Response::class, $this->response);
        
        // Null body
        $this->response->body(null);
        $this->assertInstanceOf(Response::class, $this->response);
    }

    /**
     * Test json method with complex data structures
     */
    public function testJsonWithComplexData(): void
    {
        $complexData = [
            'user' => [
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'roles' => ['admin', 'user'],
                'profile' => [
                    'bio' => 'Software developer',
                    'location' => 'New York',
                    'links' => [
                        'github' => 'https://github.com/johndoe',
                        'linkedin' => 'https://linkedin.com/in/johndoe'
                    ]
                ]
            ],
            'metadata' => [
                'timestamp' => '2023-01-01T00:00:00Z',
                'version' => '1.0.0',
                'features' => ['feature1', 'feature2']
            ]
        ];
        
        $result = $this->response->json($complexData, JSON_PRETTY_PRINT);
        $this->assertInstanceOf(Response::class, $result);
    }
}
