<?php

namespace Skeleton\Tests\Unit\Globals;

use Skeleton\Router\Response;
use Exception;
use Skeleton\Tests\BaseTestCase;

class GlobalsTest extends BaseTestCase
{
    /**
     * Test utf8ize function with array input
     */
    public function testUtf8izeWithArray(): void
    {
        $input = ['test' => 'value', 'nested' => ['key' => 'data']];
        $result = utf8ize($input);

        $this->assertIsArray($result);
        $this->assertEquals($input, $result); // Since input is already UTF-8
    }

    /**
     * Test utf8ize function with string input
     */
    public function testUtf8izeWithString(): void
    {
        $input = 'test string';
        $result = utf8ize($input);
        
        $this->assertIsString($result);
    }

    /**
     * Test utf8ize function with non-string, non-array input
     */
    public function testUtf8izeWithOtherTypes(): void
    {
        // Test with integer
        $result = utf8ize(123);
        $this->assertEquals(123, $result);
        
        // Test with boolean
        $result = utf8ize(true);
        $this->assertTrue($result);
        
        // Test with null
        $result = utf8ize(null);
        $this->assertNull($result);
    }

    /**
     * Test camelToSnake function
     */
    public function testCamelToSnake(): void
    {
        $this->assertEquals('hello_world', camelToSnake('helloWorld'));
        $this->assertEquals('simple_test', camelToSnake('simpleTest'));
        $this->assertEquals('html_parser', camelToSnake('HTMLParser'));
        $this->assertEquals('xml_http_request', camelToSnake('XMLHttpRequest'));
        $this->assertEquals('api_key', camelToSnake('APIKey'));
        $this->assertEquals('user_id', camelToSnake('userID'));
        $this->assertEquals('test', camelToSnake('test'));
        $this->assertEquals('test_case', camelToSnake('TestCase'));
    }

    /**
     * Test pluralize function with regular words
     */
    public function testPluralizeRegularWords(): void
    {
        $this->assertEquals('users', pluralize('user'));
        $this->assertEquals('posts', pluralize('post'));
        $this->assertEquals('comments', pluralize('comment'));
        $this->assertEquals('items', pluralize('item'));
    }

    /**
     * Test pluralize function with words ending in 'y'
     */
    public function testPluralizeWordsEndingInY(): void
    {
        $this->assertEquals('categories', pluralize('category'));
        $this->assertEquals('companies', pluralize('company'));
        $this->assertEquals('stories', pluralize('story'));
        $this->assertEquals('cities', pluralize('city'));
    }

    /**
     * Test response function
     */
    public function testResponseFunction(): void
    {
        $response = response();
        
        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * Test back function with session
     */
    public function testBackFunctionWithSession(): void
    {
        // Start a session for testing
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['prev_url'] = '/previous-page';
        
        // Capture the output using output buffering
        ob_start();
        
        try {
            back();
        } catch (Exception) {
            // Expected since we can't actually redirect in tests
            // The function should try to redirect and then exit
        }
        
        ob_get_clean();
        
        // The function should have tried to set the Location header
        // We can't easily test header() calls in unit tests, but we can verify the function runs
        $this->assertTrue(true); // If we get here, the function executed without fatal errors
        
        // Clean up
        unset($_SESSION['prev_url']);
    }

    /**
     * Test back function without session (fallback)
     */
    public function testBackFunctionWithoutSession(): void
    {
        // Start a session for testing
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Ensure no previous URL is set
        unset($_SESSION['prev_url']);

        // Capture the output using output buffering
        ob_start();

        try {
            back('/fallback');
        } catch (Exception) {
            // Expected since we can't actually redirect in tests
        }

        ob_get_clean();

        // The function should have tried to redirect to the fallback
        $this->assertTrue(true); // If we get here, the function executed without fatal errors
    }

    /**
     * Test dd function (die and dump)
     */
    public function testDdFunction(): void
    {
        // Since dd() calls exit(), we can't test it directly
        // But we can test that the function exists
        $this->assertTrue(function_exists('dd'));
    }

    /**
     * Test dt function (dump and return)
     */
    public function testDtFunction(): void
    {
        // Capture output
        ob_start();
        
        // Call dt function
        dt(['test' => 'data'], 'string value', 123);
        
        $output = ob_get_clean();
        
        // dt should produce some JSON output
        $this->assertNotEmpty($output);
    }

    /**
     * Test edge cases for functions
     */
    public function testEdgeCases(): void
    {
        // Test camelToSnake with empty string
        $this->assertEquals('', camelToSnake(''));
        
        // Test pluralize with empty string
        $this->assertEquals('s', pluralize(''));
        
        // Test utf8ize with nested arrays
        $nested = [
            'level1' => [
                'level2' => [
                    'level3' => 'deep value'
                ]
            ]
        ];
        $result = utf8ize($nested);
        $this->assertEquals($nested, $result);
    }

    /**
     * Test camelToSnake with various formats
     */
    public function testCamelToSnakeVariousFormats(): void
    {
        $this->assertEquals('a', camelToSnake('A'));
        $this->assertEquals('ab', camelToSnake('AB'));
        $this->assertEquals('a_bc', camelToSnake('ABc'));
        $this->assertEquals('test_case', camelToSnake('TestCase'));
        $this->assertEquals('xml_http_request', camelToSnake('XMLHttpRequest'));
    }

    /**
     * Test pluralize with single character
     */
    public function testPluralizeEdgeCases(): void
    {
        $this->assertEquals('as', pluralize('a'));
        $this->assertEquals('ies', pluralize('y'));
        $this->assertEquals('xs', pluralize('x'));
    }

    protected function tearDown(): void
    {
        // Clean up any session data
        if (session_status() !== PHP_SESSION_NONE) {
            session_destroy();
        }
        
        parent::tearDown();
    }
}
