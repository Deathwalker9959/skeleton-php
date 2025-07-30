<?php

require_once __DIR__ . '/vendor/autoload.php';

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->name('*.php');

$config = new PhpCsFixer\Config();
$config->setFinder($finder);
return $config;
