<?php

/**
 * Partfunction utf8ize($d): mixed
{
    if (is_array($d)) {
        foreach ($d as $k => $v) {
            $d[$k] = utf8ize($v);
        }
    } elseif (is_string($d)) {
        return mb_convert_encoding($d, 'UTF-8', 'UTF-8');
    }

    return $d;
}eton framework.
 */

use Skeleton\Database\Transaction;
use Skeleton\FileStorage;
use Skeleton\Singletons\FileStorageSingleton;
use Skeleton\Router\Response;
use Skeleton\Singletons\TransactionSingleton;

/**
 * Converts all string values in an array to UTF-8 encoding
 *
 * @param mixed $d The input array or string
 * @return mixed The input array with all string values converted to UTF-8 encoding
 */
function utf8ize(mixed $d)
{
    if (is_array($d)) {
        foreach ($d as $k => $v) {
            $d[$k] = utf8ize($v);
        }
    } elseif (is_string($d)) {
        return mb_convert_encoding($d, 'UTF-8');
    }

    return $d;
}

/**
 * Prints the variable(s) and ends the script
 *
 * @param mixed ...$vars The variable(s) to print and end the script with
 */
function dd(): void
{
    dt(func_get_args());
    die;
}

/**
 * Prints the variable(s)
 *
 * @param mixed ...$vars The variable(s) to print
 */
function dt(): void
{
    $result = '';
    foreach (func_get_args() as $x) {
        $result .= json_encode($x, JSON_PRETTY_PRINT);
    }

    response()->body($result)->send();
}

/**
 * Converts a camelCase string to snake_case
 *
 * @param string $input The input string in camelCase
 * @return string The input string in snake_case
 */
function camelToSnake($input): string
{
    // Handle consecutive uppercase letters (like HTML, XML, API, etc.)
    // First, separate consecutive uppercase letters from following lowercase letters
    $result = preg_replace('/([A-Z]+)([A-Z][a-z])/', '$1_$2', $input);
    // Then handle the transition from lowercase to uppercase
    $result = preg_replace('/([a-z\d])([A-Z])/', '$1_$2', (string) $result);
    
    return strtolower((string) $result);
}

/**
 * Converts a snake_case string to camelCase
 *
 * @param string $input The input string in snake_case
 * @return string The input string in camelCase
 */
function snakeToCamel($input): string
{
    return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $input))));
}

/**
 * Simple pluralization function
 *
 * @param string $word The word to pluralize
 * @return string The pluralized word
 */
function pluralize(string $word): string
{
    // Handle special cases
    $irregulars = [
        'child' => 'children',
        'person' => 'people',
        'man' => 'men',
        'woman' => 'women',
    ];

    if (isset($irregulars[$word])) {
        return $irregulars[$word];
    }

    // Default pluralization rule: add 's'
    return $word . 's';
}

/**
 * Creates and returns a new Response object
 *
 * @return Response The new Response object
 */
function response(): Response
{
    return new Response();
}

/**
 * Creates and returns a new Transaction object
 *
 * @return Transaction The new Transaction object
 */
function transaction(): Transaction
{
    return TransactionSingleton::getInstance()->getTransaction();
}

function filestorage(): FileStorage
{
    return FileStorageSingleton::getInstance()->getFileStorage();
}

/**
 * Redirects to the previous route.
 *
 * @param string $fallback The fallback URL to use if the previous route cannot be determined.
 */
function back(string $fallback = '/'): void
{
    // Check if the previous URL is stored in the session
    if (isset($_SESSION['prev_url'])) {
        // Redirect to the previous URL
        header('Location: ' . $_SESSION['prev_url']);
        exit;
    }

    // Redirect to the fallback URL
    header('Location: ' . $fallback);
    exit;
}
