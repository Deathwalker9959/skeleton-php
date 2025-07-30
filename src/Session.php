<?php

/**
 * Part of the Skeleton framework.
 */

namespace Skeleton;

class Session
{
    /**
     * Start a new session or resume an existing one.
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Get a value from the session.
     *
     * @param string $key The key of the value to get.
     * @param mixed $default The default value to return if the key does not exist.
     *
     * @return mixed The value for the given key, or the default value.
     */
    public static function get($key, mixed $default = null)
    {
        self::start();

        return $_SESSION[$key] ?? $default;
    }

    /**
     * Set a value in the session.
     *
     * @param string $key The key of the value to set.
     * @param mixed $value The value to set.
     */
    public static function set($key, mixed $value): void
    {
        self::start();

        $_SESSION[$key] = $value;
    }

    /**
     * Remove a value from the session.
     *
     * @param string $key The key of the value to remove.
     */
    public static function remove($key): void
    {
        self::start();

        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Clear all values from the session.
     */
    public static function clear(): void
    {
        self::start();

        $_SESSION = [];
    }

    /**
     * Destroy the current session.
     */
    public static function destroy(): void
    {
        self::start();

        session_unset();
        session_destroy();
    }
}
