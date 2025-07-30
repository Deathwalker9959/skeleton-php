<?php

declare(strict_types=1);

namespace Skeleton\Tests\Unit\Interfaces;

use ReflectionClass;
use Skeleton\Router\Request;
use Skeleton\Tests\BaseTestCase;
use Skeleton\Interfaces\MiddlewareInterface;

/**
 * Test case for the MiddlewareInterface
 */
class MiddlewareInterfaceTest extends BaseTestCase
{
    /**
     * Test that MiddlewareInterface exists
     */
    public function testMiddlewareInterfaceExists(): void
    {
        $this->assertTrue(interface_exists(MiddlewareInterface::class));
    }

    /**
     * Test that MiddlewareInterface has required methods
     */
    public function testMiddlewareInterfaceHasRequiredMethods(): void
    {
        $reflection = new ReflectionClass(MiddlewareInterface::class);
        
        $this->assertTrue($reflection->hasMethod('handle'));
    }

    /**
     * Test that handle method has correct signature
     */
    public function testHandleMethodSignature(): void
    {
        $reflection = new ReflectionClass(MiddlewareInterface::class);
        $handleMethod = $reflection->getMethod('handle');
        
        $this->assertFalse($handleMethod->isStatic());
        $this->assertTrue($handleMethod->isPublic());
        
        $parameters = $handleMethod->getParameters();
        $this->assertCount(2, $parameters);
        
        $this->assertEquals('request', $parameters[0]->getName());
        $this->assertEquals('models', $parameters[1]->getName());
    }

    /**
     * Test that interface is in correct namespace
     */
    public function testMiddlewareInterfaceNamespace(): void
    {
        $reflection = new ReflectionClass(MiddlewareInterface::class);
        
        $this->assertEquals('Skeleton\\Interfaces', $reflection->getNamespaceName());
        $this->assertEquals('MiddlewareInterface', $reflection->getShortName());
    }

    /**
     * Test that interface has proper documentation
     */
    public function testInterfaceDocumentation(): void
    {
        $reflection = new ReflectionClass(MiddlewareInterface::class);
        $docComment = $reflection->getDocComment();
        
        $this->assertNotFalse($docComment);
        $this->assertStringContainsString('middleware interface', $docComment);
    }

    /**
     * Test that handle method has proper documentation
     */
    public function testHandleMethodDocumentation(): void
    {
        $reflection = new ReflectionClass(MiddlewareInterface::class);
        $handleMethod = $reflection->getMethod('handle');
        $docComment = $handleMethod->getDocComment();
        
        $this->assertNotFalse($docComment);
        $this->assertStringContainsString('@param', $docComment);
        $this->assertStringContainsString('Request', $docComment);
    }

    /**
     * Test creating a mock implementation
     */
    public function testMockImplementation(): void
    {
        $mockMiddleware = new class implements MiddlewareInterface {
            public function handle(Request $request, $models): void
            {
                // Mock implementation
            }
        };

        $this->assertInstanceOf(MiddlewareInterface::class, $mockMiddleware);
    }
}
