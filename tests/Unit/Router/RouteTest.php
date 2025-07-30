<?php

declare(strict_types=1);

namespace Skeleton\Tests\Unit\Router;

use Skeleton\Tests\BaseTestCase;
use Skeleton\Router\Route;

/**
 * Test case for the Route class
 */
class RouteTest extends BaseTestCase
{
    /**
     * Test GET route creation
     */
    public function testGetRouteCreation(): void
    {
        $route = Route::Get('/users', 'UserController@index');
        
        $this->assertIsArray($route);
        $this->assertArrayHasKey('GET', $route);
        $this->assertIsArray($route['GET']);
        $this->assertCount(1, $route['GET']);
        
        $routeData = $route['GET'][0];
        $this->assertEquals('/users', $routeData['location']);
        $this->assertEquals('Skeleton\\Controllers\\UserController', $routeData['controllerClass']);
        $this->assertEquals('index', $routeData['method']);
        $this->assertIsArray($routeData['bindings']);
    }

    /**
     * Test POST route creation
     */
    public function testPostRouteCreation(): void
    {
        $route = Route::Post('/users', 'UserController@store');
        
        $this->assertIsArray($route);
        $this->assertArrayHasKey('POST', $route);
        
        $routeData = $route['POST'][0];
        $this->assertEquals('/users', $routeData['location']);
        $this->assertEquals('Skeleton\\Controllers\\UserController', $routeData['controllerClass']);
        $this->assertEquals('store', $routeData['method']);
    }

    /**
     * Test PUT route creation
     */
    public function testPutRouteCreation(): void
    {
        $route = Route::Put('/users/{id}', 'UserController@update');
        
        $this->assertIsArray($route);
        $this->assertArrayHasKey('PUT', $route);
        
        $routeData = $route['PUT'][0];
        $this->assertEquals('/users/{id}', $routeData['location']);
        $this->assertEquals('Skeleton\\Controllers\\UserController', $routeData['controllerClass']);
        $this->assertEquals('update', $routeData['method']);
    }

    /**
     * Test PATCH route creation
     */
    public function testPatchRouteCreation(): void
    {
        $route = Route::Patch('/users/{id}', 'UserController@patch');
        
        $this->assertIsArray($route);
        $this->assertArrayHasKey('PATCH', $route);
        
        $routeData = $route['PATCH'][0];
        $this->assertEquals('/users/{id}', $routeData['location']);
        $this->assertEquals('Skeleton\\Controllers\\UserController', $routeData['controllerClass']);
        $this->assertEquals('patch', $routeData['method']);
    }

    /**
     * Test DELETE route creation
     */
    public function testDeleteRouteCreation(): void
    {
        $route = Route::Delete('/users/{id}', 'UserController@destroy');
        
        $this->assertIsArray($route);
        $this->assertArrayHasKey('DELETE', $route);
        
        $routeData = $route['DELETE'][0];
        $this->assertEquals('/users/{id}', $routeData['location']);
        $this->assertEquals('Skeleton\\Controllers\\UserController', $routeData['controllerClass']);
        $this->assertEquals('destroy', $routeData['method']);
    }

    /**
     * Test route with parameters creates proper bindings
     */
    public function testRouteWithParametersCreatesBindings(): void
    {
        $route = Route::Get('/users/{id}/posts/{postId}', 'PostController@show');
        
        $routeData = $route['GET'][0];
        $this->assertIsArray($routeData['bindings']);
        
        // The bindings array should contain information about the parameters
        // The exact structure depends on how the parsing works
        $this->assertNotEmpty($routeData['bindings']);
    }

    /**
     * Test route group creation
     */
    public function testRouteGroupCreation(): void
    {
        $routes = [
            Route::Get('/users', 'UserController@index'),
            Route::Post('/users', 'UserController@store'),
        ];
        
        $group = Route::Group([
            'prefix' => 'api',
            'middleware' => 'auth'
        ], $routes);
        
        $this->assertIsArray($group);
        $this->assertEquals('group', $group['type']);
        $this->assertEquals('api', $group['prefix']);
        $this->assertEquals('auth', $group['middleware']);
        $this->assertArrayHasKey('routes', $group);
        $this->assertArrayHasKey('id', $group);
    }

    /**
     * Test route group without options
     */
    public function testRouteGroupWithoutOptions(): void
    {
        $routes = [
            Route::Get('/test', 'TestController@index'),
        ];
        
        $group = Route::Group([], $routes);
        
        $this->assertIsArray($group);
        $this->assertEquals('group', $group['type']);
        $this->assertNull($group['prefix']);
        $this->assertNull($group['middleware']);
        $this->assertArrayHasKey('routes', $group);
    }

    /**
     * Test controller string parsing
     */
    public function testControllerStringParsing(): void
    {
        $route = Route::Get('/test', 'MyController@myMethod');
        
        $routeData = $route['GET'][0];
        $this->assertEquals('Skeleton\\Controllers\\MyController', $routeData['controllerClass']);
        $this->assertEquals('myMethod', $routeData['method']);
    }

    /**
     * Test route with complex path
     */
    public function testRouteWithComplexPath(): void
    {
        $route = Route::Get('/api/v1/users/{userId}/posts/{postId}/comments', 'CommentController@index');
        
        $routeData = $route['GET'][0];
        $this->assertEquals('/api/v1/users/{userId}/posts/{postId}/comments', $routeData['location']);
        $this->assertEquals('Skeleton\\Controllers\\CommentController', $routeData['controllerClass']);
        $this->assertEquals('index', $routeData['method']);
    }

    /**
     * Test HTTP method is converted to uppercase
     */
    public function testHttpMethodIsUppercase(): void
    {
        // All route methods should create uppercase HTTP method keys
        $getRoute = Route::Get('/test', 'Controller@method');
        $postRoute = Route::Post('/test', 'Controller@method');
        $putRoute = Route::Put('/test', 'Controller@method');
        $patchRoute = Route::Patch('/test', 'Controller@method');
        $deleteRoute = Route::Delete('/test', 'Controller@method');
        
        $this->assertArrayHasKey('GET', $getRoute);
        $this->assertArrayHasKey('POST', $postRoute);
        $this->assertArrayHasKey('PUT', $putRoute);
        $this->assertArrayHasKey('PATCH', $patchRoute);
        $this->assertArrayHasKey('DELETE', $deleteRoute);
    }

    /**
     * Test route handles URL with query parameters correctly
     */
    public function testRouteHandlesUrlWithQueryParameters(): void
    {
        $route = Route::Get('/users?page=1&limit=10', 'UserController@index');
        
        $routeData = $route['GET'][0];
        // The parse_url function should extract only the path part
        $this->assertEquals('/users', $routeData['location']);
    }

    /**
     * Test multiple routes can be created
     */
    public function testMultipleRoutesCanBeCreated(): void
    {
        $route1 = Route::Get('/users', 'UserController@index');
        $route2 = Route::Post('/users', 'UserController@store');
        $route3 = Route::Put('/users/{id}', 'UserController@update');
        
        $this->assertArrayHasKey('GET', $route1);
        $this->assertArrayHasKey('POST', $route2);
        $this->assertArrayHasKey('PUT', $route3);
        
        // Each route should be independent
        $this->assertNotEquals($route1, $route2);
        $this->assertNotEquals($route2, $route3);
    }
}
