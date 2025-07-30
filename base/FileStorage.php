<?php

namespace App;

class FileStorage
{
    private $storage_path;

    public function __construct($storage_path)
    {
        $this->storage_path = $storage_path;
        if (!file_exists($storage_path)) {
            mkdir($storage_path, 0777, true);
        }
    }

    public function saveBase64Image($base64_data, $user_path = null)
    {
        if ($user_path !== null) {
            $path = $this->storage_path . '/' . $user_path;
        } else {
            $path = $this->storage_path;
        }

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        // Extract the base64 data
        list($type, $data) = explode(';', $base64_data);
        list(, $data) = explode(',', $data);
        $data = base64_decode($data);

        // Create a unique name for the image
        $file_name = uniqid() . '.jpeg';

        // Save the image to the server
        file_put_contents($path . '/' . $file_name, $data);

        return $file_name;
    }

    public function saveFile($file, $user_path = null)
    {
        if ($user_path !== null) {
            $path = $this->storage_path . '/' . $user_path;
        } else {
            $path = $this->storage_path;
        }
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        // Create a unique name for the file
        $file_name = uniqid() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);

        // Move the file to the storage path
        move_uploaded_file($file['tmp_name'], $path . '/' . $file_name);

        return $file_name;
    }

    public function deleteFile($file_name, $user_path = null)
    {
        if ($user_path !== null) {
            $path = $this->storage_path . '/' . $user_path;
        } else {
            $path = $this->storage_path;
        }

        if (file_exists($path . '/' . $file_name)) {
            unlink($path . '/' . $file_name);
        }
    }

    public function getFilePath($file_name, $user_path = null)
    {
        if ($user_path !== null) {
            $path = $this->storage_path . '/' . $user_path;
        } else {
            $path = $this->storage_path;
        }

        $filePath = preg_replace('/^.*public/', '', $path . '/' . $file_name);
        return $filePath;
    }
}
