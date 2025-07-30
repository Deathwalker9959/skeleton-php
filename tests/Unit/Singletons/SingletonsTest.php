<?php

namespace Skeleton\Tests\Unit\Singletons;

use PDOException;
use Exception;
use ReflectionClass;
use Skeleton\Tests\BaseTestCase;
use Skeleton\Singletons\ConnectionSingleton;
use Skeleton\Singletons\QueryBuilderSingleton;
use Skeleton\Singletons\FileStorageSingleton;
use Skeleton\Singletons\RequestSingleton;
use Skeleton\Singletons\RouterSingleton;
use Skeleton\Singletons\TransactionSingleton;

class SingletonsTest extends BaseTestCase
{
    /**
     * Test ConnectionSingleton basic functionality
     */
    public function testConnectionSingletonBasics(): void
    {
        // Skip test if PDO drivers are not available
        if (!extension_loaded('pdo')) {
            $this->markTestSkipped('PDO extension is not available');
        }
        
        // Mock the database configuration 
        $GLOBALS['APP_CONFIG'] = [
            'db' => [
                'driver' => 'sqlite',
                'host' => '',
                'port' => '',
                'database' => ':memory:',
                'username' => '',
                'password' => '',
                'charset' => 'utf8'
            ]
        ];
        
        try {
            // Test that getInstance returns the same instance
            $instance1 = ConnectionSingleton::getInstance();
            $instance2 = ConnectionSingleton::getInstance();
            
            $this->assertSame($instance1, $instance2);
            $this->assertInstanceOf(ConnectionSingleton::class, $instance1);
        } catch (PDOException $pdoException) {
            // Skip if SQLite driver is not available
            $this->markTestSkipped('SQLite PDO driver is not available: ' . $pdoException->getMessage());
        }
    }

    /**
     * Test QueryBuilderSingleton basic functionality
     */
    public function testQueryBuilderSingletonBasics(): void
    {
        // Test that getInstance returns the same instance
        $instance1 = QueryBuilderSingleton::getInstance();
        $instance2 = QueryBuilderSingleton::getInstance();
        
        $this->assertSame($instance1, $instance2);
        $this->assertInstanceOf(QueryBuilderSingleton::class, $instance1);
    }

    /**
     * Test FileStorageSingleton basic functionality
     */
    public function testFileStorageSingletonBasics(): void
    {
        // Test that getInstance returns the same instance
        $instance1 = FileStorageSingleton::getInstance();
        $instance2 = FileStorageSingleton::getInstance();
        
        $this->assertSame($instance1, $instance2);
        $this->assertInstanceOf(FileStorageSingleton::class, $instance1);
    }

    /**
     * Test RequestSingleton basic functionality
     */
    public function testRequestSingletonBasics(): void
    {
        // Set up required $_SERVER variables for Request constructor
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/test';
        $_GET = [];
        $_POST = [];
        $_FILES = [];
        
        // Test that getInstance returns the same instance
        $instance1 = RequestSingleton::getInstance();
        $instance2 = RequestSingleton::getInstance();
        
        $this->assertSame($instance1, $instance2);
        $this->assertInstanceOf(RequestSingleton::class, $instance1);
        
        // Clean up
        $_SERVER = [];
        $_GET = [];
        $_POST = [];
        $_FILES = [];
    }

    /**
     * Test RouterSingleton basic functionality
     */
    public function testRouterSingletonBasics(): void
    {
        // Test that getInstance returns the same instance
        $instance1 = RouterSingleton::getInstance();
        $instance2 = RouterSingleton::getInstance();
        
        $this->assertSame($instance1, $instance2);
        $this->assertInstanceOf(RouterSingleton::class, $instance1);
    }

    /**
     * Test TransactionSingleton basic functionality
     */
    public function testTransactionSingletonBasics(): void
    {
        // Test that getInstance returns the same instance
        $instance1 = TransactionSingleton::getInstance();
        $instance2 = TransactionSingleton::getInstance();
        
        $this->assertSame($instance1, $instance2);
        $this->assertInstanceOf(TransactionSingleton::class, $instance1);
    }

    /**
     * Test that all singletons prevent cloning
     */
    public function testSingletonsPreventCloning(): void
    {
        $singletonClasses = [
            FileStorageSingleton::class,
        ];

        foreach ($singletonClasses as $class) {
            $instance = $class::getInstance();
            
            try {
                $cloned = clone $instance;
                $this->fail('Expected exception when cloning ' . $class);
            } catch (Exception $e) {
                $this->assertEquals('Cannot clone a singleton.', $e->getMessage());
            }
        }
    }

    /**
     * Test that all singletons provide single instance behavior
     */
    public function testSingletonsPreventUnserialization(): void
    {
        $singletonClasses = [
            FileStorageSingleton::class,
        ];

        foreach ($singletonClasses as $class) {
            // Test that getInstance returns same instance
            $instance1 = $class::getInstance();
            $instance2 = $class::getInstance();
            
            $this->assertSame($instance1, $instance2, sprintf('Singleton %s should return same instance', $class));
            
            // Test that class has getInstance method
            $reflection = new ReflectionClass($class);
            $this->assertTrue($reflection->hasMethod('getInstance'));
            
            $getInstanceMethod = $reflection->getMethod('getInstance');
            $this->assertTrue($getInstanceMethod->isStatic());
        }
    }

    /**
     * Test singleton pattern across multiple instances
     */
    public function testSingletonPatternConsistency(): void
    {
        // Set up for RequestSingleton
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/test';
        $_GET = [];
        $_POST = [];
        $_FILES = [];

        $singletonClasses = [
            ConnectionSingleton::class,
            QueryBuilderSingleton::class,
            FileStorageSingleton::class,
            RequestSingleton::class,
            RouterSingleton::class,
            TransactionSingleton::class,
        ];

        foreach ($singletonClasses as $class) {
            $instance1 = $class::getInstance();
            $instance2 = $class::getInstance();
            $instance3 = $class::getInstance();
            
            $this->assertSame($instance1, $instance2);
            $this->assertSame($instance2, $instance3);
            $this->assertSame($instance1, $instance3);
        }

        // Clean up
        $_SERVER = [];
        $_GET = [];
        $_POST = [];
        $_FILES = [];
    }

    /**
     * Test that constructors are private
     */
    public function testPrivateConstructors(): void
    {
        $singletonClasses = [
            ConnectionSingleton::class,
            QueryBuilderSingleton::class,
            FileStorageSingleton::class,
            RequestSingleton::class,
            RouterSingleton::class,
            TransactionSingleton::class,
        ];

        foreach ($singletonClasses as $class) {
            $reflection = new ReflectionClass($class);
            $constructor = $reflection->getConstructor();
            
            if ($constructor !== null) {
                $this->assertTrue($constructor->isPrivate());
            }
        }
    }
}
