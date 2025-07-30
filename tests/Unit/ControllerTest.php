<?php

declare(strict_types=1);

namespace Skeleton\Tests\Unit;

use ReflectionClass;
use Skeleton\Tests\BaseTestCase;
use Skeleton\Controller;

/**
 * Test case for the Controller class
 */
class ControllerTest extends BaseTestCase
{
    /**
     * Test that Controller class exists
     */
    public function testControllerClassExists(): void
    {
        $this->assertTrue(class_exists(Controller::class));
    }

    /**
     * Test that Controller has middleware property
     */
    public function testControllerHasMiddlewareProperty(): void
    {
        $reflection = new ReflectionClass(Controller::class);
        
        $this->assertTrue($reflection->hasProperty('middleware'));
        
        $middlewareProperty = $reflection->getProperty('middleware');
        $this->assertTrue($middlewareProperty->isStatic());
        $this->assertTrue($middlewareProperty->isPublic());
    }

    /**
     * Test that middleware property is initially empty array
     */
    public function testMiddlewarePropertyIsInitiallyEmpty(): void
    {
        $this->assertIsArray(Controller::$middleware);
        $this->assertEmpty(Controller::$middleware);
    }

    /**
     * Test that middleware property can be modified
     */
    public function testMiddlewarePropertyCanBeModified(): void
    {
        // Store original value
        $original = Controller::$middleware;
        
        // Modify middleware
        Controller::$middleware = ['auth', 'cors'];
        
        $this->assertEquals(['auth', 'cors'], Controller::$middleware);
        
        // Restore original value
        Controller::$middleware = $original;
    }

    /**
     * Test that Controller can be instantiated
     */
    public function testControllerCanBeInstantiated(): void
    {
        $controller = new Controller();
        
        $this->assertInstanceOf(Controller::class, $controller);
    }

    /**
     * Test that Controller can be extended
     */
    public function testControllerCanBeExtended(): void
    {
        $childController = new class extends Controller {
            public static $middleware = ['custom_middleware'];
            
            public function testMethod(): string
            {
                return 'test';
            }
        };

        $this->assertInstanceOf(Controller::class, $childController);
        $this->assertEquals(['custom_middleware'], $childController::$middleware);
        $this->assertEquals('test', $childController->testMethod());
    }

    /**
     * Test Controller namespace
     */
    public function testControllerNamespace(): void
    {
        $reflection = new ReflectionClass(Controller::class);
        
        $this->assertEquals('Skeleton', $reflection->getNamespaceName());
        $this->assertEquals('Controller', $reflection->getShortName());
    }

    /**
     * Test Controller has proper documentation
     */
    public function testControllerHasDocumentation(): void
    {
        $reflection = new ReflectionClass(Controller::class);
        $docComment = $reflection->getDocComment();
        
        $this->assertNotFalse($docComment);
        $this->assertStringContainsString('base controller class', $docComment);
    }
}
