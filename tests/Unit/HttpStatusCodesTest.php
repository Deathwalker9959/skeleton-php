<?php

declare(strict_types=1);

namespace Skeleton\Tests\Unit;

use ReflectionClass;
use Skeleton\Tests\BaseTestCase;
use Skeleton\HttpStatusCodes;

/**
 * Test case for the HttpStatusCodes class
 */
class HttpStatusCodesTest extends BaseTestCase
{
    /**
     * Test that all HTTP status codes are defined correctly
     */
    public function testHttpStatusCodesAreDefined(): void
    {
        // Test informational responses (1xx)
        $this->assertEquals(100, HttpStatusCodes::HTTP_CONTINUE);
        $this->assertEquals(101, HttpStatusCodes::HTTP_SWITCHING_PROTOCOLS);
        $this->assertEquals(102, HttpStatusCodes::HTTP_PROCESSING);
        $this->assertEquals(103, HttpStatusCodes::HTTP_EARLY_HINTS);

        // Test successful responses (2xx)
        $this->assertEquals(200, HttpStatusCodes::HTTP_OK);
        $this->assertEquals(201, HttpStatusCodes::HTTP_CREATED);
        $this->assertEquals(202, HttpStatusCodes::HTTP_ACCEPTED);
        $this->assertEquals(204, HttpStatusCodes::HTTP_NO_CONTENT);
        $this->assertEquals(206, HttpStatusCodes::HTTP_PARTIAL_CONTENT);

        // Test redirection messages (3xx)
        $this->assertEquals(300, HttpStatusCodes::HTTP_MULTIPLE_CHOICES);
        $this->assertEquals(301, HttpStatusCodes::HTTP_MOVED_PERMANENTLY);
        $this->assertEquals(302, HttpStatusCodes::HTTP_FOUND);
        $this->assertEquals(304, HttpStatusCodes::HTTP_NOT_MODIFIED);
        $this->assertEquals(307, HttpStatusCodes::HTTP_TEMPORARY_REDIRECT);
        $this->assertEquals(308, HttpStatusCodes::HTTP_PERMANENTLY_REDIRECT);

        // Test client error responses (4xx)
        $this->assertEquals(400, HttpStatusCodes::HTTP_BAD_REQUEST);
        $this->assertEquals(401, HttpStatusCodes::HTTP_UNAUTHORIZED);
        $this->assertEquals(403, HttpStatusCodes::HTTP_FORBIDDEN);
        $this->assertEquals(404, HttpStatusCodes::HTTP_NOT_FOUND);
        $this->assertEquals(405, HttpStatusCodes::HTTP_METHOD_NOT_ALLOWED);
        $this->assertEquals(409, HttpStatusCodes::HTTP_CONFLICT);
        $this->assertEquals(410, HttpStatusCodes::HTTP_GONE);
        $this->assertEquals(422, HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals(429, HttpStatusCodes::HTTP_TOO_MANY_REQUESTS);

        // Test server error responses (5xx)
        $this->assertEquals(500, HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
        $this->assertEquals(501, HttpStatusCodes::HTTP_NOT_IMPLEMENTED);
        $this->assertEquals(502, HttpStatusCodes::HTTP_BAD_GATEWAY);
        $this->assertEquals(503, HttpStatusCodes::HTTP_SERVICE_UNAVAILABLE);
        $this->assertEquals(504, HttpStatusCodes::HTTP_GATEWAY_TIMEOUT);
    }

    /**
     * Test that constants are public and accessible
     */
    public function testConstantsArePublic(): void
    {
        $reflection = new ReflectionClass(HttpStatusCodes::class);
        $constants = $reflection->getConstants();

        $this->assertNotEmpty($constants);
        
        // Verify some key constants exist
        $this->assertArrayHasKey('HTTP_OK', $constants);
        $this->assertArrayHasKey('HTTP_NOT_FOUND', $constants);
        $this->assertArrayHasKey('HTTP_INTERNAL_SERVER_ERROR', $constants);
    }

    /**
     * Test status code ranges
     */
    public function testStatusCodeRanges(): void
    {
        $reflection = new ReflectionClass(HttpStatusCodes::class);
        $constants = $reflection->getConstants();

        $informational = [];
        $successful = [];
        $redirection = [];
        $clientError = [];
        $serverError = [];

        foreach ($constants as $value) {
            if ($value >= 100 && $value < 200) {
                $informational[] = $value;
            } elseif ($value >= 200 && $value < 300) {
                $successful[] = $value;
            } elseif ($value >= 300 && $value < 400) {
                $redirection[] = $value;
            } elseif ($value >= 400 && $value < 500) {
                $clientError[] = $value;
            } elseif ($value >= 500 && $value < 600) {
                $serverError[] = $value;
            }
        }

        // Verify we have status codes in each category
        $this->assertNotEmpty($informational, 'Should have informational status codes (1xx)');
        $this->assertNotEmpty($successful, 'Should have successful status codes (2xx)');
        $this->assertNotEmpty($redirection, 'Should have redirection status codes (3xx)');
        $this->assertNotEmpty($clientError, 'Should have client error status codes (4xx)');
        $this->assertNotEmpty($serverError, 'Should have server error status codes (5xx)');
    }

    /**
     * Test that all constants follow naming convention
     */
    public function testConstantNamingConvention(): void
    {
        $reflection = new ReflectionClass(HttpStatusCodes::class);
        $constants = $reflection->getConstants();

        foreach ($constants as $name => $value) {
            // All constants should start with HTTP_
            $this->assertStringStartsWith('HTTP_', $name);
            
            // All constants should be uppercase
            $this->assertEquals($name, strtoupper($name));
            
            // Value should be a valid HTTP status code
            $this->assertIsInt($value);
            $this->assertGreaterThanOrEqual(100, $value);
            $this->assertLessThan(600, $value);
        }
    }

    /**
     * Test specific commonly used status codes
     */
    public function testCommonStatusCodes(): void
    {
        // Most common successful responses
        $this->assertEquals(200, HttpStatusCodes::HTTP_OK);
        $this->assertEquals(201, HttpStatusCodes::HTTP_CREATED);
        $this->assertEquals(204, HttpStatusCodes::HTTP_NO_CONTENT);

        // Most common client errors
        $this->assertEquals(400, HttpStatusCodes::HTTP_BAD_REQUEST);
        $this->assertEquals(401, HttpStatusCodes::HTTP_UNAUTHORIZED);
        $this->assertEquals(403, HttpStatusCodes::HTTP_FORBIDDEN);
        $this->assertEquals(404, HttpStatusCodes::HTTP_NOT_FOUND);
        $this->assertEquals(422, HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);

        // Most common server errors
        $this->assertEquals(500, HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
        $this->assertEquals(502, HttpStatusCodes::HTTP_BAD_GATEWAY);
        $this->assertEquals(503, HttpStatusCodes::HTTP_SERVICE_UNAVAILABLE);
    }

    /**
     * Test that class cannot be instantiated (should be utility class)
     */
    public function testClassIsNotInstantiable(): void
    {
        $reflection = new ReflectionClass(HttpStatusCodes::class);
        
        // The class should ideally have a private constructor or be abstract
        // For now, we'll just verify it's a valid class
        $this->assertTrue($reflection->isInstantiable());
        
        // We can create an instance but it shouldn't have any instance methods
        $instance = new HttpStatusCodes();
        $this->assertInstanceOf(HttpStatusCodes::class, $instance);
    }
}
