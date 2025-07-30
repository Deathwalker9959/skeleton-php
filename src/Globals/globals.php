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
 * Comprehensive pluralization function
 *
 * @param string $word The word to pluralize
 * @return string The pluralized word
 */
function pluralize(string $word): string
{
    // Handle empty string
    if (empty($word)) {
        return 's';
    }
    
    $lowerWord = strtolower($word);
    
    // Handle irregular plurals
    $irregulars = [
        'child' => 'children',
        'person' => 'people',
        'man' => 'men',
        'woman' => 'women',
        'tooth' => 'teeth',
        'foot' => 'feet',
        'mouse' => 'mice',
        'goose' => 'geese',
        'ox' => 'oxen',
        'sheep' => 'sheep',
        'deer' => 'deer',
        'fish' => 'fish',
    ];

    if (isset($irregulars[$lowerWord])) {
        return $irregulars[$lowerWord];
    }

    $length = strlen($word);
    $lastChar = substr($word, -1);
    $lastTwoChars = $length > 1 ? substr($word, -2) : '';
    $precedingChar = $length > 1 ? substr($word, -2, 1) : '';
    $vowels = ['a', 'e', 'i', 'o', 'u'];

    // Words ending in 'y' preceded by a consonant: change 'y' to 'ies'
    if ($lastChar === 'y') {
        if ($length === 1 || !in_array(strtolower($precedingChar), $vowels)) {
            return substr($word, 0, -1) . 'ies';
        }
    }

    // Words ending in 'f' or 'fe': change to 'ves'
    if ($lastChar === 'f') {
        return substr($word, 0, -1) . 'ves';
    }
    if ($lastTwoChars === 'fe') {
        return substr($word, 0, -2) . 'ves';
    }

    // Words ending in 's', 'ss', 'sh', 'ch', 'x', 'z': add 'es'
    // Exception: single letter words ending in 'x' or 'z' just add 's'
    if (preg_match('/([sxz]|sh|ch)$/', $word)) {
        if ($length === 1 && ($lastChar === 'x' || $lastChar === 'z')) {
            return "{$word}s";
        }
        return "{$word}es";
    }

    // Words ending in 'o' preceded by a consonant: add 'es'
    if ($lastChar === 'o' && $length > 1) {
        if (!in_array(strtolower($precedingChar), $vowels)) {
            return "{$word}es";
        }
    }

    // Default pluralization rule: add 's'
    return "{$word}s";
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
