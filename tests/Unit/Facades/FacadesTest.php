<?php

namespace Skeleton\Tests\Unit\Facades;

use Skeleton\Tests\BaseTestCase;
use Skeleton\Facades\Image;
use Skeleton\Facades\Password;

class FacadesTest extends BaseTestCase
{
    /**
     * Test Image facade exists
     */
    public function testImageFacadeExists(): void
    {
        $this->assertTrue(class_exists(Image::class));
    }

    /**
     * Test Password facade exists
     */
    public function testPasswordFacadeExists(): void
    {
        $this->assertTrue(class_exists(Password::class));
    }

    /**
     * Test Image facade has required methods
     */
    public function testImageFacadeMethods(): void
    {
        $this->assertTrue(method_exists(Image::class, 'encode'));
        $this->assertTrue(method_exists(Image::class, 'decode'));
        $this->assertTrue(method_exists(Image::class, 'size'));
    }

    /**
     * Test Password facade has required methods
     */
    public function testPasswordFacadeMethods(): void
    {
        $this->assertTrue(method_exists(Password::class, 'hash'));
        $this->assertTrue(method_exists(Password::class, 'verify'));
    }

    /**
     * Test Image encode functionality
     */
    public function testImageEncodeFunctionality(): void
    {
        $data = 'Hello World';
        $encoded = Image::encode($data);
        
        $this->assertIsString($encoded);
        $this->assertEquals(base64_encode($data), $encoded);
    }

    /**
     * Test Image decode functionality
     */
    public function testImageDecodeFunctionality(): void
    {
        $data = 'Hello World';
        $encoded = base64_encode($data);
        $decoded = Image::decode($encoded);
        
        $this->assertEquals($data, $decoded);
        
        // Test invalid base64
        $invalid = Image::decode('invalid-base64!!!');
        $this->assertFalse($invalid);
    }

    /**
     * Test Password hash functionality
     */
    public function testPasswordHashFunctionality(): void
    {
        $password = 'test_password_123';
        $hash = Password::hash($password);
        
        $this->assertIsString($hash);
        $this->assertNotEmpty($hash);
        $this->assertNotEquals($password, $hash);
    }

    /**
     * Test Password verify functionality
     */
    public function testPasswordVerifyFunctionality(): void
    {
        $password = 'test_password_123';
        $hash = Password::hash($password);
        
        // Test correct password verification
        $this->assertTrue(Password::verify($password, $hash));
        
        // Test incorrect password verification
        $this->assertFalse(Password::verify('wrong_password', $hash));
    }
}
