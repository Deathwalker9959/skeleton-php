<?php

declare(strict_types=1);

namespace Skeleton\Tests\Unit\Interfaces;

use ReflectionClass;
use Skeleton\Singleton;
use Skeleton\Tests\BaseTestCase;
use Skeleton\Interfaces\SingletonInterface;

/**
 * Test case for the SingletonInterface
 */
class SingletonInterfaceTest extends BaseTestCase
{
    /**
     * Test that SingletonInterface exists
     */
    public function testSingletonInterfaceExists(): void
    {
        $this->assertTrue(interface_exists(SingletonInterface::class));
    }

    /**
     * Test that SingletonInterface has required methods
     */
    public function testSingletonInterfaceHasRequiredMethods(): void
    {
        $reflection = new ReflectionClass(SingletonInterface::class);
        
        $this->assertTrue($reflection->hasMethod('getInstance'));
        $this->assertTrue($reflection->hasMethod('__clone'));
        $this->assertTrue($reflection->hasMethod('__wakeup'));
    }

    /**
     * Test that getInstance method is static
     */
    public function testGetInstanceMethodIsStatic(): void
    {
        $reflection = new ReflectionClass(SingletonInterface::class);
        $getInstanceMethod = $reflection->getMethod('getInstance');
        
        $this->assertTrue($getInstanceMethod->isStatic());
    }

    /**
     * Test that interface is in correct namespace
     */
    public function testSingletonInterfaceNamespace(): void
    {
        $reflection = new ReflectionClass(SingletonInterface::class);
        
        $this->assertEquals('Skeleton\\Interfaces', $reflection->getNamespaceName());
        $this->assertEquals('SingletonInterface', $reflection->getShortName());
    }

    /**
     * Test that interface methods have correct return types in docblocks
     */
    public function testMethodDocumentation(): void
    {
        $reflection = new ReflectionClass(SingletonInterface::class);
        
        $getInstanceMethod = $reflection->getMethod('getInstance');
        $getInstanceDoc = $getInstanceMethod->getDocComment();
        
        $this->assertNotFalse($getInstanceDoc);
        $this->assertStringContainsString('@return static', $getInstanceDoc);
    }

    /**
     * Test that Singleton class implements this interface
     */
    public function testSingletonImplementsInterface(): void
    {
        $singletonReflection = new ReflectionClass(Singleton::class);
        
        $this->assertTrue($singletonReflection->implementsInterface(SingletonInterface::class));
    }
}
