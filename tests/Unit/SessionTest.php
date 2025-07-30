<?php

declare(strict_types=1);

namespace Skeleton\Tests\Unit;

use Skeleton\Tests\BaseTestCase;
use Skeleton\Session;

/**
 * Test case for the Session class
 */
class SessionTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Clear any existing session data
        if (session_status() !== PHP_SESSION_NONE) {
            session_destroy();
        }
        
        // Clear $_SESSION superglobal
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        // Clean up session after each test
        if (session_status() !== PHP_SESSION_NONE) {
            session_destroy();
        }

        $_SESSION = [];

        parent::tearDown();
    }

    /**
     * Test that start() starts a session when none exists
     */
    public function testStartStartsSessionWhenNoneExists(): void
    {
        // Ensure no session is active
        $this->assertEquals(PHP_SESSION_NONE, session_status());
        
        Session::start();
        
        $this->assertEquals(PHP_SESSION_ACTIVE, session_status());
    }

    /**
     * Test that start() doesn't start a new session when one already exists
     */
    public function testStartDoesNotStartNewSessionWhenActive(): void
    {
        Session::start();
        $initialSessionId = session_id();
        
        Session::start();
        
        $this->assertEquals($initialSessionId, session_id());
        $this->assertEquals(PHP_SESSION_ACTIVE, session_status());
    }

    /**
     * Test setting and getting session values
     */
    public function testSetAndGet(): void
    {
        Session::set('test_key', 'test_value');
        
        $this->assertEquals('test_value', Session::get('test_key'));
    }

    /**
     * Test getting non-existent key returns default value
     */
    public function testGetNonExistentKeyReturnsDefault(): void
    {
        $this->assertNull(Session::get('non_existent_key'));
        $this->assertEquals('default', Session::get('non_existent_key', 'default'));
    }

    /**
     * Test setting various data types
     */
    public function testSetVariousDataTypes(): void
    {
        $testData = [
            'string' => 'hello world',
            'integer' => 42,
            'float' => 3.14,
            'boolean' => true,
            'array' => ['a', 'b', 'c'],
            'object' => (object) ['prop' => 'value']
        ];

        foreach ($testData as $key => $value) {
            Session::set($key, $value);
            $this->assertEquals($value, Session::get($key));
        }
    }

    /**
     * Test removing session values
     */
    public function testRemove(): void
    {
        Session::set('test_key', 'test_value');
        $this->assertEquals('test_value', Session::get('test_key'));
        
        Session::remove('test_key');
        $this->assertNull(Session::get('test_key'));
    }

    /**
     * Test removing non-existent key doesn't cause errors
     */
    public function testRemoveNonExistentKey(): void
    {
        // This should not throw any errors
        Session::remove('non_existent_key');
        
        $this->assertNull(Session::get('non_existent_key'));
    }

    /**
     * Test clearing all session data
     */
    public function testClear(): void
    {
        Session::set('key1', 'value1');
        Session::set('key2', 'value2');
        Session::set('key3', 'value3');
        
        $this->assertEquals('value1', Session::get('key1'));
        $this->assertEquals('value2', Session::get('key2'));
        $this->assertEquals('value3', Session::get('key3'));
        
        Session::clear();
        
        $this->assertNull(Session::get('key1'));
        $this->assertNull(Session::get('key2'));
        $this->assertNull(Session::get('key3'));
    }

    /**
     * Test destroying session
     */
    public function testDestroy(): void
    {
        Session::set('test_key', 'test_value');
        $this->assertEquals(PHP_SESSION_ACTIVE, session_status());
        
        Session::destroy();
        
        $this->assertEquals(PHP_SESSION_NONE, session_status());
    }

    /**
     * Test session methods work in sequence
     */
    public function testSessionWorkflow(): void
    {
        // Start with no session
        $this->assertEquals(PHP_SESSION_NONE, session_status());
        
        // Set some values
        Session::set('user_id', 123);
        Session::set('username', 'testuser');
        
        // Verify session is active and values are set
        $this->assertEquals(PHP_SESSION_ACTIVE, session_status());
        $this->assertEquals(123, Session::get('user_id'));
        $this->assertEquals('testuser', Session::get('username'));
        
        // Remove one value
        Session::remove('user_id');
        $this->assertNull(Session::get('user_id'));
        $this->assertEquals('testuser', Session::get('username'));
        
        // Clear all
        Session::clear();
        $this->assertNull(Session::get('username'));
        
        // Session should still be active after clear
        $this->assertEquals(PHP_SESSION_ACTIVE, session_status());
        
        // Destroy completely
        Session::destroy();
        $this->assertEquals(PHP_SESSION_NONE, session_status());
    }

    /**
     * Test that session data persists between method calls
     */
    public function testSessionDataPersistence(): void
    {
        Session::set('persistent_key', 'persistent_value');
        
        // Simulate multiple requests by calling get multiple times
        for ($i = 0; $i < 5; $i++) {
            $this->assertEquals('persistent_value', Session::get('persistent_key'));
        }
    }
}
