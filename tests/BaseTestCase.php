<?php

declare(strict_types=1);

namespace Skeleton\Tests;

use Skeleton\Singletons\ConnectionSingleton;
use Skeleton\Singletons\FileStorageSingleton;
use Skeleton\Singletons\QueryBuilderSingleton;
use Skeleton\Singletons\RequestSingleton;
use Skeleton\Singletons\RouterSingleton;
use Skeleton\Singletons\TransactionSingleton;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use PHPUnit\Framework\TestCase;
use Mockery;

/**
 * Base test case class that all test classes should extend
 */
abstract class BaseTestCase extends TestCase
{
    /**
     * Called before each test method
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Reset singletons before each test
        $this->resetSingletons();
    }

    /**
     * Called after each test method
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Reset all singleton instances for clean test state
     */
    protected function resetSingletons(): void
    {
        // Use reflection to reset singleton instances
        $singletonClasses = [
            ConnectionSingleton::class,
            FileStorageSingleton::class,
            QueryBuilderSingleton::class,
            RequestSingleton::class,
            RouterSingleton::class,
            TransactionSingleton::class,
        ];

        foreach ($singletonClasses as $class) {
            if (class_exists($class)) {
                $reflection = new ReflectionClass($class);
                if ($reflection->hasProperty('instance')) {
                    $instanceProperty = $reflection->getProperty('instance');
                    $instanceProperty->setAccessible(true);
                    $instanceProperty->setValue(null, null);
                }
            }
        }
    }

    /**
     * Get a private or protected method from a class for testing
     */
    protected function getPrivateMethod(object $object, string $methodName): ReflectionMethod
    {
        $reflection = new ReflectionClass($object);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * Get a private or protected property from a class for testing
     */
    protected function getPrivateProperty(object $object, string $propertyName): ReflectionProperty
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        return $property;
    }

    /**
     * Set a private or protected property value
     */
    protected function setPrivateProperty(object $object, string $propertyName, mixed $value): void
    {
        $property = $this->getPrivateProperty($object, $propertyName);
        $property->setValue($object, $value);
    }
}
