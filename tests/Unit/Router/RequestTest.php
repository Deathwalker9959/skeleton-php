<?php

namespace Skeleton\Tests\Unit\Router;

use ReflectionClass;
use Skeleton\Tests\BaseTestCase;
use Skeleton\Router\Request;

class RequestTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Clear superglobals
        $_SERVER = [];
        $_GET = [];
        $_POST = [];
        $_FILES = [];
        $_REQUEST = [];
    }

    /**
     * Test request construction with GET method
     */
    public function testGetRequest(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/users';
        $_GET = ['page' => '1', 'limit' => '10'];
        $_POST = [];
        $_FILES = [];
        
        $request = new Request();
        
        $this->assertEquals('GET', $request->method);
        $this->assertEquals('/users', $request->uri);
        $this->assertEquals(['page' => '1', 'limit' => '10'], $request->query);
        $this->assertEquals([], $request->post);
        $this->assertEquals([], $request->files);
    }

    /**
     * Test request construction with POST method
     */
    public function testPostRequest(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/users/create';
        $_GET = [];
        $_POST = ['name' => 'John Doe', 'email' => 'john@example.com'];
        $_FILES = [];
        
        $request = new Request();
        
        $this->assertEquals('POST', $request->method);
        $this->assertEquals('/users/create', $request->uri);
        $this->assertEquals([], $request->query);
        $this->assertEquals(['name' => 'John Doe', 'email' => 'john@example.com'], $request->post);
    }

    /**
     * Test input method with form data
     */
    public function testInputMethodWithFormData(): void
    {
        $_REQUEST = ['name' => 'John Doe', 'email' => 'john@example.com'];
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/users';
        $_GET = [];
        $_POST = ['name' => 'John Doe', 'email' => 'john@example.com'];
        $_FILES = [];
        
        $request = new Request();
        
        $this->assertEquals('John Doe', $request->input('name'));
        $this->assertEquals('john@example.com', $request->input('email'));
        $this->assertEquals('default', $request->input('missing', 'default'));
        $this->assertNull($request->input('missing'));
    }

    /**
     * Test input method with array data
     */
    public function testInputMethodWithArrayData(): void
    {
        $_REQUEST = ['tags' => ['php', 'framework', 'web']];
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/posts';
        $_GET = [];
        $_POST = ['tags' => ['php', 'framework', 'web']];
        $_FILES = [];
        
        $request = new Request();
        
        $result = $request->input('tags');
        $this->assertIsArray($result);
        $this->assertEquals(['php', 'framework', 'web'], $result);
    }

    /**
     * Test input method with JSON data
     */
    public function testInputMethodWithJsonData(): void
    {
        $_REQUEST = [];
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/api/users';
        $_GET = [];
        $_POST = [];
        $_FILES = [];
        
        $request = new Request();
        
        // Simulate JSON body
        $reflectionClass = new ReflectionClass($request);
        $bodyProperty = $reflectionClass->getProperty('body');
        $bodyProperty->setAccessible(true);
        $bodyProperty->setValue($request, '{"name":"Jane Doe","email":"jane@example.com"}');
        
        $this->assertEquals('Jane Doe', $request->input('name'));
        $this->assertEquals('jane@example.com', $request->input('email'));
        $this->assertEquals('default', $request->input('missing', 'default'));
    }

    /**
     * Test file method
     */
    public function testFileMethod(): void
    {
        $_FILES = [
            'avatar' => [
                'name' => 'profile.jpg',
                'tmp_name' => '/tmp/phpupload',
                'size' => 1024,
                'error' => 0
            ]
        ];
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/upload';
        $_GET = [];
        $_POST = [];
        
        $request = new Request();
        
        $file = $request->file('avatar');
        $this->assertIsArray($file);
        $this->assertEquals('profile.jpg', $file['name']);
        $this->assertEquals('/tmp/phpupload', $file['tmp_name']);
        
        $this->assertEquals('default', $request->file('missing', 'default'));
        $this->assertNull($request->file('missing'));
    }

    /**
     * Test isMethod method
     */
    public function testIsMethod(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/test';
        $_GET = [];
        $_POST = [];
        $_FILES = [];
        
        $request = new Request();
        
        $this->assertTrue($request->isMethod('POST'));
        $this->assertTrue($request->isMethod('post'));
        $this->assertFalse($request->isMethod('GET'));
        $this->assertFalse($request->isMethod('PUT'));
    }

    /**
     * Test request with file uploads
     */
    public function testRequestWithFileUploads(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/upload';
        $_GET = [];
        $_POST = ['title' => 'Upload Test'];
        $_FILES = [
            'document' => [
                'name' => 'test.pdf',
                'tmp_name' => '/tmp/phpupload1',
                'size' => 2048,
                'error' => 0
            ],
            'image' => [
                'name' => 'test.jpg',
                'tmp_name' => '/tmp/phpupload2',
                'size' => 1024,
                'error' => 0
            ]
        ];
        
        $request = new Request();
        
        $this->assertEquals('POST', $request->method);
        $this->assertEquals('/upload', $request->uri);
        $this->assertEquals(['title' => 'Upload Test'], $request->post);
        $this->assertArrayHasKey('document', $request->files);
        $this->assertArrayHasKey('image', $request->files);
        $this->assertEquals('test.pdf', $request->files['document']['name']);
        $this->assertEquals('test.jpg', $request->files['image']['name']);
    }

    protected function tearDown(): void
    {
        // Clean up superglobals
        $_SERVER = [];
        $_GET = [];
        $_POST = [];
        $_FILES = [];
        $_REQUEST = [];
        
        parent::tearDown();
    }
}
