<?php

namespace Skeleton\Tests\Integration;

use ReflectionClass;
use Skeleton\Tests\BaseTestCase;
use Skeleton\Router\Request;
use Skeleton\Router\Response;
use Skeleton\Router\RequestValidator;
use Skeleton\Router\MiddlewareResponse;
use Skeleton\Router\Route;

class RouterIntegrationTest extends BaseTestCase
{
    /**
     * Test complete request/response cycle
     */
    public function testRequestResponseCycle(): void
    {
        // Create a request with POST data
        $_REQUEST = ['name' => 'John Doe', 'email' => 'john@example.com'];
        $_POST = ['name' => 'John Doe', 'email' => 'john@example.com'];
        $_GET = ['page' => '1'];
        $_FILES = [];
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/users/create';
        
        $request = new Request();
        
        // Test request properties
        $this->assertEquals('POST', $request->method);
        $this->assertEquals('/users/create', $request->uri);
        $this->assertEquals(['name' => 'John Doe', 'email' => 'john@example.com'], $request->post);
        $this->assertEquals(['page' => '1'], $request->query);
        
        // Test input method
        $this->assertEquals('John Doe', $request->input('name'));
        $this->assertEquals('john@example.com', $request->input('email'));
        $this->assertEquals('default', $request->input('missing', 'default'));
        
        // Test file method
        $this->assertEquals('default', $request->file('avatar', 'default'));
        
        // Test isMethod
        $this->assertTrue($request->isMethod('POST'));
        $this->assertFalse($request->isMethod('GET'));
        
        // Create response
        $response = new Response();
        $response->status(201)
                ->header('Content-Type', 'application/json')
                ->body(['message' => 'User created successfully']);
        
        // Test response properties
        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * Test request validation
     */
    public function testRequestValidation(): void
    {
        $_REQUEST = ['name' => 'John Doe', 'email' => 'john@example.com'];
        $_POST = ['name' => 'John Doe', 'email' => 'john@example.com'];
        $_FILES = [];
        
        $request = new Request();
        
        // Test request validation
        $this->assertTrue(RequestValidator::validate($request));
        
        // Test input validation
        $this->assertTrue(RequestValidator::validateInput($request, 'name'));
        $this->assertFalse(RequestValidator::validateInput($request, 'missing'));
        
        // Test multiple input validation
        $this->assertTrue(RequestValidator::validateInputKeys($request, ['name', 'email']));
        $this->assertFalse(RequestValidator::validateInputKeys($request, ['name', 'missing']));
    }

    /**
     * Test middleware response
     */
    public function testMiddlewareResponse(): void
    {
        $response = new Response();
        $response->body('Test content');
        
        // Test successful middleware response
        $middlewareResponse = new MiddlewareResponse(true, $response);
        $this->assertTrue($middlewareResponse->getResult());
        $this->assertInstanceOf(Response::class, $middlewareResponse->getResponse());
        
        // Test failed middleware response
        $failedResponse = new MiddlewareResponse(false);
        $this->assertFalse($failedResponse->getResult());
        $this->assertNull($failedResponse->getResponse());
    }

    /**
     * Test route creation and parsing
     */
    public function testRouteCreation(): void
    {
        // Test GET route
        $getRoute = Route::get('/users', 'UserController@index');
        $this->assertIsArray($getRoute);
        $this->assertArrayHasKey('GET', $getRoute);
        
        // Test POST route
        $postRoute = Route::post('/users', 'UserController@store');
        $this->assertIsArray($postRoute);
        $this->assertArrayHasKey('POST', $postRoute);
        
        // Test PUT route
        $putRoute = Route::put('/users/{id}', 'UserController@update');
        $this->assertIsArray($putRoute);
        $this->assertArrayHasKey('PUT', $putRoute);
        
        // Test PATCH route
        $patchRoute = Route::patch('/users/{id}', 'UserController@patch');
        $this->assertIsArray($patchRoute);
        $this->assertArrayHasKey('PATCH', $patchRoute);
        
        // Test DELETE route
        $deleteRoute = Route::delete('/users/{id}', 'UserController@destroy');
        $this->assertIsArray($deleteRoute);
        $this->assertArrayHasKey('DELETE', $deleteRoute);
    }

    /**
     * Test route with parameters
     */
    public function testRouteWithParameters(): void
    {
        $route = Route::get('/users/{id}/posts/{postId}', 'UserController@showPost');
        
        $this->assertIsArray($route);
        $this->assertArrayHasKey('GET', $route);
        $this->assertArrayHasKey(0, $route['GET']);
        
        $routeData = $route['GET'][0];
        $this->assertEquals('/users/{id}/posts/{postId}', $routeData['location']);
        $this->assertEquals('Skeleton\Controllers\UserController', $routeData['controllerClass']);
        $this->assertEquals('showPost', $routeData['method']);
        $this->assertArrayHasKey('bindings', $routeData);
    }

    /**
     * Test route groups
     */
    public function testRouteGroups(): void
    {
        $routes = [
            Route::get('/users', 'UserController@index'),
            Route::post('/users', 'UserController@store')
        ];
        
        $group = Route::Group([
            'prefix' => '/api/v1',
            'middleware' => ['auth', 'throttle']
        ], $routes);
        
        $this->assertIsArray($group);
        $this->assertEquals('group', $group['type']);
        $this->assertEquals('/api/v1', $group['prefix']);
        $this->assertEquals(['auth', 'throttle'], $group['middleware']);
        $this->assertArrayHasKey('routes', $group);
    }

    /**
     * Test response methods
     */
    public function testResponseMethods(): void
    {
        $response = new Response();
        
        // Test status method
        $response->status(404);
        $this->assertInstanceOf(Response::class, $response);
        
        // Test header method
        $response->header('X-Custom-Header', 'custom-value');
        $this->assertInstanceOf(Response::class, $response);
        
        // Test body method
        $response->body('Response content');
        $this->assertInstanceOf(Response::class, $response);
        
        // Test json method
        $response->json(['key' => 'value'], 0, 200);
        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * Test request with JSON body
     */
    public function testRequestWithJsonBody(): void
    {
        // Simulate JSON request
        $_REQUEST = [];
        $_POST = [];
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        
        $jsonData = '{"name":"John","email":"john@example.com"}';
        
        $request = new Request();
        // Manually set the body for testing
        $reflectionClass = new ReflectionClass($request);
        $bodyProperty = $reflectionClass->getProperty('body');
        $bodyProperty->setAccessible(true);
        $bodyProperty->setValue($request, $jsonData);
        
        // Test input method with JSON data
        $this->assertEquals('John', $request->input('name'));
        $this->assertEquals('john@example.com', $request->input('email'));
        $this->assertNull($request->input('missing'));
    }

    protected function tearDown(): void
    {
        // Clean up superglobals
        $_REQUEST = [];
        $_POST = [];
        $_GET = [];
        $_FILES = [];
        $_SERVER = [];
        
        parent::tearDown();
    }
}
