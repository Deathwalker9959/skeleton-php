<?php

/**
 * Part of the Skeleton framework.
 */

namespace Skeleton\Facades;

/**
 * A class for handling password hashing and verification
 */
class Password
{
    /**
     * Hashes a password using the default algorithm
     *
     * @param string $password The password to hash
     * @return string The hashed password
     */
    public static function hash(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Verifies that a password matches a hash
     *
     * @param string $password The password to verify
     * @param string $hash The hash to compare the password to
     * @return bool True if the password matches the hash, false otherwise
     */
    public static function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
