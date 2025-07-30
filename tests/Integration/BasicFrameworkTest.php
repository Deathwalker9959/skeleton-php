<?php

declare(strict_types=1);

namespace Skeleton\Tests\Integration;

use Exception;
use Skeleton\Tests\BaseTestCase;
use Skeleton\Session;
use Skeleton\HttpStatusCodes;

/**
 * Integration tests for basic framework functionality
 */
class BasicFrameworkTest extends BaseTestCase
{
    /**
     * Test that Session and HttpStatusCodes work together
     */
    public function testSessionAndHttpStatusCodesIntegration(): void
    {
        // Set a session value with an HTTP status code
        Session::set('last_status', HttpStatusCodes::HTTP_OK);
        
        $this->assertEquals(200, Session::get('last_status'));
        
        // Test with different status codes
        Session::set('error_status', HttpStatusCodes::HTTP_NOT_FOUND);
        Session::set('success_status', HttpStatusCodes::HTTP_CREATED);
        
        $this->assertEquals(404, Session::get('error_status'));
        $this->assertEquals(201, Session::get('success_status'));
        
        // Clean up
        Session::clear();
    }

    /**
     * Test that multiple framework components can be used together
     */
    public function testMultipleComponentsIntegration(): void
    {
        // Use Session to store data
        Session::set('user_id', 123);
        Session::set('username', 'testuser');
        
        // Use HttpStatusCodes for status tracking
        $successStatus = HttpStatusCodes::HTTP_OK;
        $notFoundStatus = HttpStatusCodes::HTTP_NOT_FOUND;
        
        // Verify integration
        $this->assertEquals(123, Session::get('user_id'));
        $this->assertEquals('testuser', Session::get('username'));
        $this->assertEquals(200, $successStatus);
        $this->assertEquals(404, $notFoundStatus);
        
        // Test session workflow
        Session::set('status', $successStatus);
        $this->assertEquals(200, Session::get('status'));
        
        Session::remove('status');
        $this->assertNull(Session::get('status'));
        
        // Clean up
        Session::destroy();
    }

    /**
     * Test that the framework constants are properly defined
     */
    public function testFrameworkConstants(): void
    {
        // Test HTTP status codes are integers
        $this->assertIsInt(HttpStatusCodes::HTTP_OK);
        $this->assertIsInt(HttpStatusCodes::HTTP_NOT_FOUND);
        $this->assertIsInt(HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
        
        // Test they have expected values
        $this->assertEquals(200, HttpStatusCodes::HTTP_OK);
        $this->assertEquals(404, HttpStatusCodes::HTTP_NOT_FOUND);
        $this->assertEquals(500, HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test error handling workflow
     */
    public function testErrorHandlingWorkflow(): void
    {
        // Simulate an error workflow using session and status codes
        try {
            // Simulate some operation that might fail
            $result = $this->simulateOperation(true); // Will "succeed"
            
            Session::set('operation_status', HttpStatusCodes::HTTP_OK);
            Session::set('operation_result', $result);
            
            $this->assertEquals(200, Session::get('operation_status'));
            $this->assertEquals('success', Session::get('operation_result'));
            
        } catch (Exception $exception) {
            Session::set('operation_status', HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
            Session::set('operation_error', $exception->getMessage());
        }
        
        // Test failure case
        try {
            $result = $this->simulateOperation(false); // Will "fail"
        } catch (Exception $exception) {
            Session::set('operation_status', HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
            Session::set('operation_error', $exception->getMessage());
            
            $this->assertEquals(500, Session::get('operation_status'));
            $this->assertEquals('Operation failed', Session::get('operation_error'));
        }
        
        Session::clear();
    }

    /**
     * Helper method to simulate an operation
     */
    private function simulateOperation(bool $shouldSucceed): string
    {
        if ($shouldSucceed) {
            return 'success';
        }
        
        throw new Exception('Operation failed');
    }

    /**
     * Test session persistence across multiple operations
     */
    public function testSessionPersistenceAcrossOperations(): void
    {
        // Start with empty session
        Session::clear();
        
        // First operation
        $this->performOperation('op1', 'data1');
        $this->assertEquals('DATA1', Session::get('op1_result')); // Expect uppercase
        
        // Second operation
        $this->performOperation('op2', 'data2');
        $this->assertEquals('DATA1', Session::get('op1_result')); // Still there
        $this->assertEquals('DATA2', Session::get('op2_result'));
        
        // Third operation
        $this->performOperation('op3', 'data3');
        $this->assertEquals('DATA1', Session::get('op1_result')); // Still there
        $this->assertEquals('DATA2', Session::get('op2_result')); // Still there
        $this->assertEquals('DATA3', Session::get('op3_result'));
        
        Session::destroy();
    }

    /**
     * Helper method to perform an operation and store result in session
     */
    private function performOperation(string $name, string $data): void
    {
        // Simulate some work
        $result = strtoupper($data);
        
        // Store in session
        Session::set($name . '_result', $result);
        Session::set($name . '_status', HttpStatusCodes::HTTP_OK);
    }
}
