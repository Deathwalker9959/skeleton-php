<?php

namespace Skeleton\Tests\Unit\Router;

use Skeleton\Tests\BaseTestCase;
use Skeleton\Router\RequestValidator;
use Skeleton\Router\Request;

class RequestValidatorTest extends BaseTestCase
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
     * Test validate method with valid request
     */
    public function testValidateWithValidRequest(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/test';
        $_POST = ['name' => 'John', 'email' => 'john@example.com'];
        $_FILES = [];
        
        $request = new Request();
        
        $result = RequestValidator::validate($request);
        $this->assertTrue($result);
    }

    /**
     * Test validate method with invalid post data
     */
    public function testValidateWithInvalidPostData(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/test';
        $_POST = 'invalid'; // Not an array
        $_FILES = [];
        
        $request = new Request();
        
        $result = RequestValidator::validate($request);
        $this->assertFalse($result);
    }

    /**
     * Test validate method with invalid files data
     */
    public function testValidateWithInvalidFilesData(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/test';
        $_POST = ['name' => 'John'];
        $_FILES = 'invalid'; // Not an array
        
        $request = new Request();
        
        $result = RequestValidator::validate($request);
        $this->assertFalse($result);
    }

    /**
     * Test validateInput method with valid string input
     */
    public function testValidateInputWithValidString(): void
    {
        $_REQUEST = ['name' => 'John Doe'];
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/test';
        $_POST = ['name' => 'John Doe'];
        $_FILES = [];
        
        $request = new Request();
        
        $result = RequestValidator::validateInput($request, 'name');
        $this->assertTrue($result);
    }

    /**
     * Test validateInput method with empty input
     */
    public function testValidateInputWithEmptyInput(): void
    {
        $_REQUEST = ['name' => ''];
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/test';
        $_POST = ['name' => ''];
        $_FILES = [];
        
        $request = new Request();
        
        $result = RequestValidator::validateInput($request, 'name');
        $this->assertFalse($result);
    }

    /**
     * Test validateInput method with non-string input
     */
    public function testValidateInputWithNonStringInput(): void
    {
        $_REQUEST = ['data' => ['array', 'value']];
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/test';
        $_POST = ['data' => ['array', 'value']];
        $_FILES = [];
        
        $request = new Request();
        
        $result = RequestValidator::validateInput($request, 'data');
        $this->assertFalse($result);
    }

    /**
     * Test validateInput method with missing input
     */
    public function testValidateInputWithMissingInput(): void
    {
        $_REQUEST = [];
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/test';
        $_POST = [];
        $_FILES = [];
        
        $request = new Request();
        
        $result = RequestValidator::validateInput($request, 'missing');
        $this->assertFalse($result);
    }

    /**
     * Test validateInput method with default value
     */
    public function testValidateInputWithDefaultValue(): void
    {
        $_REQUEST = [];
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/test';
        $_POST = [];
        $_FILES = [];

        $request = new Request();

        $result = RequestValidator::validateInput($request, 'missing', 'default');
        $this->assertTrue($result); // Default value is a string
    }

    /**
     * Test validateInputKeys method with all valid keys
     */
    public function testValidateInputKeysWithValidKeys(): void
    {
        $_REQUEST = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Hello world'
        ];
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/test';
        $_POST = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Hello world'
        ];
        $_FILES = [];
        
        $request = new Request();
        
        $result = RequestValidator::validateInputKeys($request, ['name', 'email', 'message']);
        $this->assertTrue($result);
    }

    /**
     * Test validateInputKeys method with some invalid keys
     */
    public function testValidateInputKeysWithInvalidKeys(): void
    {
        $_REQUEST = [
            'name' => 'John Doe',
            'email' => '', // Empty value
            'message' => 'Hello world'
        ];
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/test';
        $_POST = [
            'name' => 'John Doe',
            'email' => '',
            'message' => 'Hello world'
        ];
        $_FILES = [];
        
        $request = new Request();
        
        $result = RequestValidator::validateInputKeys($request, ['name', 'email', 'message']);
        $this->assertFalse($result);
    }

    /**
     * Test validateInputKeys method with missing keys
     */
    public function testValidateInputKeysWithMissingKeys(): void
    {
        $_REQUEST = [
            'name' => 'John Doe'
        ];
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/test';
        $_POST = [
            'name' => 'John Doe'
        ];
        $_FILES = [];
        
        $request = new Request();
        
        $result = RequestValidator::validateInputKeys($request, ['name', 'email', 'message']);
        $this->assertFalse($result);
    }

    /**
     * Test validateInputKeys method with empty array
     */
    public function testValidateInputKeysWithEmptyArray(): void
    {
        $_REQUEST = ['name' => 'John Doe'];
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/test';
        $_POST = ['name' => 'John Doe'];
        $_FILES = [];

        $request = new Request();

        $result = RequestValidator::validateInputKeys($request, []);
        $this->assertTrue($result); // No keys to validate, so it passes
    }

    /**
     * Test validator with whitespace-only input
     */
    public function testValidateInputWithWhitespaceOnly(): void
    {
        $_REQUEST = ['name' => '   '];
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/test';
        $_POST = ['name' => '   '];
        $_FILES = [];

        $request = new Request();

        $result = RequestValidator::validateInput($request, 'name');
        $this->assertFalse($result); // Whitespace-only should be considered empty
    }

    /**
     * Test validator with numeric string input
     */
    public function testValidateInputWithNumericString(): void
    {
        $_REQUEST = ['age' => '25'];
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/test';
        $_POST = ['age' => '25'];
        $_FILES = [];

        $request = new Request();

        $result = RequestValidator::validateInput($request, 'age');
        $this->assertTrue($result); // Numeric string is still a string
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
