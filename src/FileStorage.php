<?php

/**
 * Part of the Skeleton framework.
 */

namespace Skeleton;

class FileStorage
{
    public function __construct(private string $storage_path)
    {
        if (!file_exists($this->storage_path)) {
            mkdir($this->storage_path, 0777, true);
        }
    }

    public function saveBase64Image(string $base64_data, ?string $user_path = null): string
    {
        $path = $user_path !== null ? $this->storage_path . '/' . $user_path : $this->storage_path;

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        // Extract the base64 data
        [$type, $data] = explode(';', $base64_data);
        [, $data] = explode(',', $data);
        $data = base64_decode($data);

        // Create a unique name for the image
        $file_name = uniqid() . '.jpeg';

        // Save the image to the server
        file_put_contents($path . '/' . $file_name, $data);

        return $file_name;
    }

    public function saveFile(array $file, ?string $user_path = null): string
    {
        $path = $user_path !== null ? $this->storage_path . '/' . $user_path : $this->storage_path;

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        // Create a unique name for the file
        $file_name = uniqid() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);

        // Move the file to the storage path
        move_uploaded_file($file['tmp_name'], $path . '/' . $file_name);

        return $file_name;
    }

    public function deleteFile(string $file_name, ?string $user_path = null): void
    {
        $path = $user_path !== null ? $this->storage_path . '/' . $user_path : $this->storage_path;

        if (file_exists($path . '/' . $file_name)) {
            unlink($path . '/' . $file_name);
        }
    }

    public function getFilePath(string $file_name, ?string $user_path = null): ?string
    {
        $path = $user_path !== null ? $this->storage_path . '/' . $user_path : $this->storage_path;
        return preg_replace('/^.*public/', '', $path . '/' . $file_name);
    }
}
