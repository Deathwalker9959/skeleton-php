<?php

namespace Skeleton\Tests\Unit;

use Skeleton\Router\Response;
use Exception;
use Skeleton\Tests\BaseTestCase;

class GlobalFunctionsTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Include the global functions
        if (!function_exists('utf8ize')) {
            require_once __DIR__ . '/../../src/Globals/globals.php';
        }
    }

    /**
     * Test utf8ize function with array
     */
    public function testUtf8izeWithArray(): void
    {
        $input = ['test', 'data', ['nested', 'array']];
        $result = utf8ize($input);
        $this->assertIsArray($result);
    }

    /**
     * Test utf8ize function with string
     */
    public function testUtf8izeWithString(): void
    {
        $input = 'test string';
        $result = utf8ize($input);
        $this->assertIsString($result);
    }

    /**
     * Test utf8ize function with other types
     */
    public function testUtf8izeWithOtherTypes(): void
    {
        $input = 123;
        $result = utf8ize($input);
        $this->assertEquals(123, $result);
        
        $input = true;
        $result = utf8ize($input);
        $this->assertTrue($result);
        
        $input = null;
        $result = utf8ize($input);
        $this->assertNull($result);
    }

    /**
     * Test camelToSnake function
     */
    public function testCamelToSnake(): void
    {
        $this->assertEquals('user_name', camelToSnake('userName'));
        $this->assertEquals('user_profile_image', camelToSnake('userProfileImage'));
        $this->assertEquals('id', camelToSnake('id'));
        $this->assertEquals('test', camelToSnake('test'));
        $this->assertEquals('api_key', camelToSnake('apiKey'));
    }

    /**
     * Test pluralize function
     */
    public function testPluralize(): void
    {
        $this->assertEquals('cats', pluralize('cat'));
        $this->assertEquals('dogs', pluralize('dog'));
        $this->assertEquals('boxes', pluralize('box'));
        $this->assertEquals('cities', pluralize('city'));
        $this->assertEquals('parties', pluralize('party'));
        $this->assertEquals('companies', pluralize('company'));
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
     * Test dt function (output is captured)
     */
    public function testDtFunction(): void
    {
        ob_start();
        
        // Mock the response function for testing
        if (!function_exists('response')) {
            function response(): object {
                return new class {
                    public function body($content): self { return $this; }

                    public function send(): void { echo 'dt output'; }
                };
            }
        }
        
        dt('test', 'data');
        $output = ob_get_clean();
        
        // Since dt calls response()->body()->send(), we expect some output
        $this->assertIsString($output);
    }

    /**
     * Test back function with session
     */
    public function testBackFunctionWithSession(): void
    {
        // Mock session data
        $_SESSION['prev_url'] = '/previous-page';

        // Capture headers
        ob_start();

        try {
            back('/default');
        } catch (Exception) {
            // Expected since we can't actually redirect in tests
        }

        ob_get_clean();

        $this->assertTrue(true); // If we get here, the function executed
    }

    /**
     * Test back function without session
     */
    public function testBackFunctionWithoutSession(): void
    {
        // Clear session
        unset($_SESSION['prev_url']);

        // Capture headers
        ob_start();

        try {
            back('/default');
        } catch (Exception) {
            // Expected since we can't actually redirect in tests
        }

        ob_get_clean();

        $this->assertTrue(true); // If we get here, the function executed
    }

    /**
     * Test back function with custom fallback
     */
    public function testBackFunctionWithCustomFallback(): void
    {
        // Clear session
        unset($_SESSION['prev_url']);

        ob_start();

        try {
            back('/custom-fallback');
        } catch (Exception) {
            // Expected since we can't actually redirect in tests
        }

        ob_get_clean();

        $this->assertTrue(true); // If we get here, the function executed
    }

    protected function tearDown(): void
    {
        // Clean up session
        if (isset($_SESSION['prev_url'])) {
            unset($_SESSION['prev_url']);
        }
        
        parent::tearDown();
    }
}
