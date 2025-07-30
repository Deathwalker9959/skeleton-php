<?php

namespace App\Facades;

/**
 * A class for handling image encoding and decoding
 */
class Image
{
    /**
     * Encodes an image into base64 format
     *
     * @param string $data The image data to encode
     * @return string The base64-encoded image data
     */
    public static function encode(string $data)
    {
        return base64_encode($data);
    }

    /**
     * Decodes base64-encoded image data
     *
     * @param string $data The base64-encoded image data
     * @return string|bool The decoded image data or false if the data is invalid
     */
    public static function decode(string $data)
    {
        return base64_decode($data, true);
    }

    /**
     * Calculates the size of a string in bytes
     *
     * @param string $data The string to calculate the size of
     * @return int The size of the string in bytes
     */
    public static function size(string $data)
    {
        $size = 0;
        if (function_exists('mb_strlen')) {
            $size = mb_strlen($data, '8bit');
        } else {
            $size = strlen($data);
        }

        return $size;
    }
}
