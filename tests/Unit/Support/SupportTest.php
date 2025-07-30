<?php

namespace Skeleton\Tests\Unit\Support;

use PDOException;
use Skeleton\Tests\BaseTestCase;
use Skeleton\Router\Response;
use Skeleton\Database\Transaction;
use Skeleton\FileStorage;

class SupportTest extends BaseTestCase
{
    /**
     * Test utf8ize function with string input
     */
    public function testUtf8izeString(): void
    {
        $input = "test string";
        $result = utf8ize($input);
        $this->assertIsString($result);
    }

    /**
     * Test utf8ize function with array input
     */
    public function testUtf8izeArray(): void
    {
        $input = ['key1' => 'value1', 'key2' => 'value2'];
        $result = utf8ize($input);
        $this->assertIsArray($result);
        $this->assertEquals('value1', $result['key1']);
        $this->assertEquals('value2', $result['key2']);
    }

    /**
     * Test utf8ize function with nested array
     */
    public function testUtf8izeNestedArray(): void
    {
        $input = ['nested' => ['key' => 'value']];
        $result = utf8ize($input);
        $this->assertIsArray($result);
        $this->assertEquals('value', $result['nested']['key']);
    }

    /**
     * Test camelToSnake function
     */
    public function testCamelToSnake(): void
    {
        // Basic camelCase
        $this->assertEquals('hello_world', camelToSnake('helloWorld'));
        $this->assertEquals('test_case', camelToSnake('testCase'));
        
        // Consecutive uppercase letters
        $this->assertEquals('html_parser', camelToSnake('HTMLParser'));
        $this->assertEquals('xml_document', camelToSnake('XMLDocument'));
        $this->assertEquals('api_response', camelToSnake('APIResponse'));
        
        // Single uppercase letter
        $this->assertEquals('a_test', camelToSnake('ATest'));
        
        // Already snake_case
        $this->assertEquals('already_snake', camelToSnake('already_snake'));
        
        // Empty string
        $this->assertEquals('', camelToSnake(''));
    }

    /**
     * Test snakeToCamel function
     */
    public function testSnakeToCamel(): void
    {
        $this->assertEquals('helloWorld', snakeToCamel('hello_world'));
        $this->assertEquals('testCase', snakeToCamel('test_case'));
        $this->assertEquals('simpleTest', snakeToCamel('simple_test'));
        
        // Single word
        $this->assertEquals('test', snakeToCamel('test'));
        
        // Empty string
        $this->assertEquals('', snakeToCamel(''));
    }

    /**
     * Test pluralize function
     */
    public function testPluralize(): void
    {
        // Regular words
        $this->assertEquals('tests', pluralize('test'));
        $this->assertEquals('items', pluralize('item'));
        
        // Words ending in 'y'
        $this->assertEquals('categories', pluralize('category'));
        $this->assertEquals('stories', pluralize('story'));
        
        // Single letter
        $this->assertEquals('as', pluralize('a'));
        
        // Empty string
        $this->assertEquals('s', pluralize(''));
    }

    /**
     * Test response function returns Response object
     */
    public function testResponseFunction(): void
    {
        $response = response();
        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * Test transaction function returns Transaction object
     */
    public function testTransactionFunction(): void
    {
        // Skip if database is not available
        if (!extension_loaded('pdo') || !class_exists('\PDO')) {
            $this->markTestSkipped('PDO extension not available');
        }
        
        try {
            $transaction = transaction();
            $this->assertInstanceOf(Transaction::class, $transaction);
        } catch (PDOException $pdoException) {
            $this->markTestSkipped('Database not configured: ' . $pdoException->getMessage());
        }
    }

    /**
     * Test filestorage function returns FileStorage object
     */
    public function testFileStorageFunction(): void
    {
        $fileStorage = filestorage();
        $this->assertInstanceOf(FileStorage::class, $fileStorage);
    }

    /**
     * Test back function exists and is callable
     */
    public function testBackFunctionExists(): void
    {
        $this->assertTrue(function_exists('back'));
        $this->assertTrue(is_callable('back'));
    }

    /**
     * Test dd function exists and is callable
     */
    public function testDdFunctionExists(): void
    {
        $this->assertTrue(function_exists('dd'));
        $this->assertTrue(is_callable('dd'));
    }

    /**
     * Test dt function exists and is callable
     */
    public function testDtFunctionExists(): void
    {
        $this->assertTrue(function_exists('dt'));
        $this->assertTrue(is_callable('dt'));
    }

    /**
     * Test all global functions are available
     */
    public function testGlobalFunctionsAvailable(): void
    {
        $functions = [
            'utf8ize',
            'dd',
            'dt',
            'camelToSnake',
            'snakeToCamel',
            'pluralize',
            'response',
            'transaction',
            'filestorage',
            'back'
        ];

        foreach ($functions as $function) {
            $this->assertTrue(function_exists($function), sprintf('Function %s should exist', $function));
        }
    }
}
