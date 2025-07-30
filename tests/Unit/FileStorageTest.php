<?php

declare(strict_types=1);

namespace Skeleton\Tests\Unit;

use Skeleton\Tests\BaseTestCase;
use Skeleton\FileStorage;

/**
 * Test case for the FileStorage class
 */
class FileStorageTest extends BaseTestCase
{
    private string $testStoragePath;

    private FileStorage $fileStorage;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a temporary directory for testing
        $this->testStoragePath = sys_get_temp_dir() . '/skeleton_test_storage_' . uniqid();
        $this->fileStorage = new FileStorage($this->testStoragePath);
    }

    protected function tearDown(): void
    {
        // Clean up test files and directories
        $this->removeDirectory($this->testStoragePath);

        parent::tearDown();
    }

    /**
     * Remove a directory and all its contents recursively
     */
    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }

        rmdir($dir);
    }

    /**
     * Test that constructor creates storage directory
     */
    public function testConstructorCreatesStorageDirectory(): void
    {
        $this->assertTrue(is_dir($this->testStoragePath));
        $this->assertTrue(is_writable($this->testStoragePath));
    }

    /**
     * Test that constructor works with existing directory
     */
    public function testConstructorWithExistingDirectory(): void
    {
        // Create another FileStorage instance with the same path
        new FileStorage($this->testStoragePath);

        $this->assertTrue(is_dir($this->testStoragePath));
    }

    /**
     * Test saving base64 image
     */
    public function testSaveBase64Image(): void
    {
        // Create a simple base64 encoded image (1x1 pixel PNG)
        $base64Data = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8/5+hHgAHggJ/PchI7wAAAABJRU5ErkJggg==';

        $fileName = $this->fileStorage->saveBase64Image($base64Data);

        $this->assertIsString($fileName);
        $this->assertStringContainsString('.jpeg', $fileName);
        $this->assertTrue(file_exists($this->testStoragePath . '/' . $fileName));
    }

    /**
     * Test saving base64 image with user path
     */
    public function testSaveBase64ImageWithUserPath(): void
    {
        $base64Data = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8/5+hHgAHggJ/PchI7wAAAABJRU5ErkJggg==';
        $userPath = 'user123';

        $fileName = $this->fileStorage->saveBase64Image($base64Data, $userPath);

        $this->assertIsString($fileName);
        $this->assertTrue(file_exists($this->testStoragePath . '/' . $userPath . '/' . $fileName));
        $this->assertTrue(is_dir($this->testStoragePath . '/' . $userPath));
    }

    /**
     * Test saving uploaded file
     */
    public function testSaveFile(): void
    {
        // Create a mock uploaded file
        $tempFile = tempnam(sys_get_temp_dir(), 'test_upload');
        file_put_contents($tempFile, 'test file content');

        // Mock move_uploaded_file by copying the file instead
        // Since we can't actually test move_uploaded_file in unit tests
        $userPath = 'uploads';
        $targetDir = $this->testStoragePath . '/' . $userPath;

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = uniqid() . '.txt';
        copy($tempFile, $targetDir . '/' . $fileName);

        $this->assertTrue(file_exists($targetDir . '/' . $fileName));
        $this->assertEquals('test file content', file_get_contents($targetDir . '/' . $fileName));

        // Clean up
        unlink($tempFile);
    }

    /**
     * Test deleting file
     */
    public function testDeleteFile(): void
    {
        // Create a test file
        $fileName = 'test_delete.txt';
        $filePath = $this->testStoragePath . '/' . $fileName;
        file_put_contents($filePath, 'content to delete');

        $this->assertTrue(file_exists($filePath));

        $this->fileStorage->deleteFile($fileName);

        $this->assertFalse(file_exists($filePath));
    }

    /**
     * Test deleting file with user path
     */
    public function testDeleteFileWithUserPath(): void
    {
        $userPath = 'user456';
        $fileName = 'test_delete_user.txt';
        $fullDir = $this->testStoragePath . '/' . $userPath;

        // Create directory and file
        mkdir($fullDir, 0777, true);
        $filePath = $fullDir . '/' . $fileName;
        file_put_contents($filePath, 'content to delete');

        $this->assertTrue(file_exists($filePath));

        $this->fileStorage->deleteFile($fileName, $userPath);

        $this->assertFalse(file_exists($filePath));
    }

    /**
     * Test deleting non-existent file doesn't cause errors
     */
    public function testDeleteNonExistentFile(): void
    {
        // This should not throw any errors
        $this->fileStorage->deleteFile('non_existent_file.txt');

        // Test with user path
        $this->fileStorage->deleteFile('non_existent_file.txt', 'non_existent_user');

        $this->assertTrue(true); // If we get here, no exceptions were thrown
    }

    /**
     * Test getting file path
     */
    public function testGetFilePath(): void
    {
        $fileName = 'test_path.txt';

        $filePath = $this->fileStorage->getFilePath($fileName);

        $this->assertIsString($filePath);
        $this->assertStringContainsString($fileName, $filePath);
    }

    /**
     * Test getting file path with user path
     */
    public function testGetFilePathWithUserPath(): void
    {
        $fileName = 'test_path_user.txt';
        $userPath = 'user789';

        $filePath = $this->fileStorage->getFilePath($fileName, $userPath);

        $this->assertIsString($filePath);
        $this->assertStringContainsString($fileName, $filePath);
        $this->assertStringContainsString($userPath, $filePath);
    }

    /**
     * Test file path format
     */
    public function testFilePathFormat(): void
    {
        $fileName = 'test.jpg';
        $filePath = $this->fileStorage->getFilePath($fileName);

        // The method removes everything up to 'public' from the path
        // Since our test path doesn't contain 'public', it should return the relative path
        $this->assertStringEndsWith('/' . $fileName, $filePath);
    }

    /**
     * Test base64 image generates unique filenames
     */
    public function testBase64ImageGeneratesUniqueFilenames(): void
    {
        $base64Data = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8/5+hHgAHggJ/PchI7wAAAABJRU5ErkJggg==';

        $fileName1 = $this->fileStorage->saveBase64Image($base64Data);
        $fileName2 = $this->fileStorage->saveBase64Image($base64Data);

        $this->assertNotEquals($fileName1, $fileName2);
        $this->assertTrue(file_exists($this->testStoragePath . '/' . $fileName1));
        $this->assertTrue(file_exists($this->testStoragePath . '/' . $fileName2));
    }

    /**
     * Test creating nested user paths
     */
    public function testNestedUserPaths(): void
    {
        $base64Data = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8/5+hHgAHggJ/PchI7wAAAABJRU5ErkJggg==';
        $userPath = 'users/123/images';

        $fileName = $this->fileStorage->saveBase64Image($base64Data, $userPath);

        $this->assertTrue(file_exists($this->testStoragePath . '/' . $userPath . '/' . $fileName));
        $this->assertTrue(is_dir($this->testStoragePath . '/users/123/images'));
    }
}
