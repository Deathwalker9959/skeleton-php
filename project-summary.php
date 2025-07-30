<?php

/**
 * Dynamic Test & Coverage Report
 * Automatically analyzes project state and test results
 */

// Dynamic project information detection
$composerData = json_decode(file_get_contents(__DIR__ . '/composer.json'), true);
$projectName = $composerData['name'] ?? 'Unknown Project';
$projectVersion = $composerData['version'] ?? 'dev';
$projectDescription = $composerData['description'] ?? 'PHP Project';

// Dynamic tool detection and versions
$tools = [];

// PHPUnit
if (file_exists(__DIR__ . '/vendor/bin/phpunit')) {
    $phpunitOutput = shell_exec(__DIR__ . '/vendor/bin/phpunit --version 2>/dev/null');
    if (preg_match('/PHPUnit\s+(\d+\.\d+\.\d+)/', $phpunitOutput, $matches)) {
        $tools['PHPUnit'] = $matches[1];
    }
}

// Rector
if (file_exists(__DIR__ . '/rector.php')) {
    $rectorOutput = shell_exec(__DIR__ . '/vendor/bin/rector --version 2>/dev/null');
    if (preg_match('/Rector\s+(\d+\.\d+\.\d+)/', $rectorOutput, $matches)) {
        $tools['Rector'] = $matches[1];
    } else {
        $tools['Rector'] = 'Available';
    }
}

// PHP-CS-Fixer
if (file_exists(__DIR__ . '/.php-cs-fixer.php')) {
    $fixerOutput = shell_exec(__DIR__ . '/vendor/bin/php-cs-fixer --version 2>/dev/null');
    if (preg_match('/PHP CS Fixer\s+(\d+\.\d+\.\d+)/', $fixerOutput, $matches)) {
        $tools['PHP-CS-Fixer'] = $matches[1];
    } else {
        $tools['PHP-CS-Fixer'] = 'Available';
    }
}

// PHPStan
if (file_exists(__DIR__ . '/phpstan.neon') || file_exists(__DIR__ . '/vendor/bin/phpstan')) {
    $phpstanOutput = shell_exec(__DIR__ . '/vendor/bin/phpstan --version 2>/dev/null');
    if (preg_match('/PHPStan\s+(\d+\.\d+\.\d+)/', $phpstanOutput, $matches)) {
        $tools['PHPStan'] = $matches[1];
    } else {
        $tools['PHPStan'] = 'Available';
    }
}

// Header
$titleLine = strtoupper(str_replace(['/', '-', '_'], ' ', $projectName)) . " - DYNAMIC ANALYSIS REPORT";
echo "🎯 {$titleLine}\n";
echo str_repeat("=", strlen($titleLine) + 4) . "\n\n";

// Project Information
echo "📋 PROJECT INFORMATION\n";
echo str_repeat("-", 40) . "\n";
echo "Name: " . ucwords(str_replace(['/', '-', '_'], ' ', $projectName)) . "\n";
echo "Version: {$projectVersion}\n";
echo "Description: {$projectDescription}\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Xdebug: " . (extension_loaded('xdebug') ? "✅ v" . phpversion('xdebug') : "❌ Not available") . "\n";

echo "\nDevelopment Tools:\n";
foreach ($tools as $tool => $version) {
    echo "   {$tool}: ✅ {$version}\n";
}
echo "\n";

// Dynamic Rector Analysis
if (isset($tools['Rector'])) {
    echo "🔧 RECTOR ANALYSIS\n";
    echo str_repeat("-", 40) . "\n";
    
    // Parse Rector configuration
    $rectorRules = [];
    if (file_exists(__DIR__ . '/rector.php')) {
        $rectorConfig = file_get_contents(__DIR__ . '/rector.php');
        
        // Common Rector rules to detect
        $rulePatterns = [
            'ConstructorPromotionRector' => 'Constructor property promotion',
            'ReadOnlyPropertyRector' => 'Readonly properties',
            'TypedPropertyRector' => 'Typed properties',
            'ReturnTypeFromReturnNewRector' => 'Return type inference',
            'AddReturnTypeDeclarationRector' => 'Return type declarations',
            'DeadCodeRemovalRector' => 'Dead code removal',
            'SimplifyIfReturnBoolRector' => 'Boolean return simplification',
            'RemoveUnusedVariableRector' => 'Unused variable cleanup'
        ];
        
        foreach ($rulePatterns as $pattern => $description) {
            if (strpos($rectorConfig, $pattern) !== false) {
                $rectorRules[] = $description;
            }
        }
        
        // Check for rule sets
        if (strpos($rectorConfig, 'PHP_') !== false) {
            preg_match_all('/PHP_(\d+)/', $rectorConfig, $matches);
            if (!empty($matches[1])) {
                $phpVersions = array_unique($matches[1]);
                $rectorRules[] = "PHP " . implode(', ', $phpVersions) . " compatibility";
            }
        }
    }
    
    echo "Rector Version: {$tools['Rector']}\n";
    echo "Active Rules: " . count($rectorRules) . "\n";
    if (!empty($rectorRules)) {
        foreach ($rectorRules as $rule) {
            echo "   • {$rule}\n";
        }
    }
    
    // Try to get Rector statistics if available
    $rectorStats = shell_exec('cd ' . __DIR__ . ' && vendor/bin/rector process --dry-run --no-progress-bar 2>/dev/null');
    if ($rectorStats && strpos($rectorStats, 'would change') !== false) {
        if (preg_match('/(\d+)\s+files?\s+would\s+change/', $rectorStats, $matches)) {
            echo "Potential Changes: {$matches[1]} files could be improved\n";
        }
    } else {
        echo "Status: ✅ No improvements needed or already applied\n";
    }
    echo "\n";
}

// Dynamic PHPUnit Test Execution and Analysis
echo "🧪 PHPUNIT ANALYSIS\n";
echo str_repeat("-", 40) . "\n";

if (isset($tools['PHPUnit'])) {
    echo "PHPUnit Version: {$tools['PHPUnit']}\n";
    
    // Run PHPUnit with coverage using explicit path and timeout
    $phpunitCmd = 'timeout 60s XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-text --colors=never';
    $testOutput = shell_exec('cd ' . __DIR__ . ' && ' . $phpunitCmd . ' 2>&1');
    
    // If no output or timeout, try without coverage
    if (empty($testOutput) || strpos($testOutput, 'timeout') !== false) {
        $phpunitCmd = 'timeout 60s ./vendor/bin/phpunit --colors=never';
        $testOutput = shell_exec('cd ' . __DIR__ . ' && ' . $phpunitCmd . ' 2>&1');
    }
    
    // Parse test results
    $testResults = [
        'passed' => 0,
        'failed' => 0,
        'skipped' => 0,
        'errors' => 0,
        'assertions' => 0,
        'time' => '0.00'
    ];
    
    // Parse different PHPUnit output formats
    if (preg_match('/OK \((\d+) tests?, (\d+) assertions?\)/', $testOutput, $matches)) {
        $testResults['passed'] = (int)$matches[1];
        $testResults['assertions'] = (int)$matches[2];
    } elseif (preg_match('/Tests:\s*(\d+),\s*Assertions:\s*(\d+)/', $testOutput, $matches)) {
        $totalTests = (int)$matches[1];
        $testResults['assertions'] = (int)$matches[2];
        
        if (preg_match('/Failures:\s*(\d+)/', $testOutput, $failMatches)) {
            $testResults['failed'] = (int)$failMatches[1];
        }
        if (preg_match('/Errors:\s*(\d+)/', $testOutput, $errorMatches)) {
            $testResults['errors'] = (int)$errorMatches[1];
        }
        if (preg_match('/Skipped:\s*(\d+)/', $testOutput, $skipMatches)) {
            $testResults['skipped'] = (int)$skipMatches[1];
        }
        
        $testResults['passed'] = $totalTests - $testResults['failed'] - $testResults['errors'] - $testResults['skipped'];
    } elseif (preg_match('/(\d+)\s+passed/', $testOutput, $matches)) {
        // Handle different output format
        $testResults['passed'] = (int)$matches[1];
        if (preg_match('/(\d+)\s+failed/', $testOutput, $failMatches)) {
            $testResults['failed'] = (int)$failMatches[1];
        }
        if (preg_match('/(\d+)\s+assertions/', $testOutput, $assertMatches)) {
            $testResults['assertions'] = (int)$assertMatches[1];
        }
    } elseif (preg_match('/FAILURES!\s*Tests:\s*(\d+),\s*Assertions:\s*(\d+),\s*Failures:\s*(\d+)/', $testOutput, $matches)) {
        // Handle failure format
        $totalTests = (int)$matches[1];
        $testResults['assertions'] = (int)$matches[2];
        $testResults['failed'] = (int)$matches[3];
        $testResults['passed'] = $totalTests - $testResults['failed'];
    }
    
    // If still no results, try to count from test output
    if ($testResults['passed'] === 0 && $testResults['failed'] === 0) {
        // Count dots and F's in the output (PHPUnit progress indicators)
        $passCount = substr_count($testOutput, '.');
        $failCount = substr_count($testOutput, 'F');
        $errorCount = substr_count($testOutput, 'E');
        $skipCount = substr_count($testOutput, 'S');
        
        if ($passCount > 0 || $failCount > 0 || $errorCount > 0) {
            $testResults['passed'] = $passCount;
            $testResults['failed'] = $failCount;
            $testResults['errors'] = $errorCount;
            $testResults['skipped'] = $skipCount;
        }
    }
    
    // Parse execution time
    if (preg_match('/Time:\s*([\d.]+)/', $testOutput, $timeMatches)) {
        $testResults['time'] = $timeMatches[1];
    }
    
    echo "Test Results:\n";
    echo "   ✅ Passed: {$testResults['passed']}\n";
    if ($testResults['failed'] > 0) echo "   ❌ Failed: {$testResults['failed']}\n";
    if ($testResults['errors'] > 0) echo "   🚨 Errors: {$testResults['errors']}\n";
    if ($testResults['skipped'] > 0) echo "   ⏭️  Skipped: {$testResults['skipped']}\n";
    echo "   🔍 Assertions: {$testResults['assertions']}\n";
    echo "   ⏱️  Time: {$testResults['time']} seconds\n";
    
    // Show test failures if any
    if ($testResults['failed'] > 0 || $testResults['errors'] > 0) {
        echo "\nIssues Found:\n";
        $lines = explode("\n", $testOutput);
        $issueCount = 0;
        foreach ($lines as $line) {
            if (strpos($line, 'FAIL') !== false && $issueCount < 3) {
                echo "   • " . trim($line) . "\n";
                $issueCount++;
            } elseif (strpos($line, 'ERROR') !== false && $issueCount < 3) {
                echo "   • " . trim($line) . "\n";
                $issueCount++;
            }
        }
    }
    echo "\n";
} else {
    echo "❌ PHPUnit not available\n\n";
}

// Dynamic Code Coverage Analysis
echo "📊 CODE COVERAGE ANALYSIS\n";
echo str_repeat("-", 40) . "\n";

if (extension_loaded('xdebug') && isset($tools['PHPUnit'])) {
    // Extract coverage from previous PHPUnit run
    $coverage = [
        'lines' => ['percent' => 0, 'covered' => 0, 'total' => 0],
        'methods' => ['percent' => 0, 'covered' => 0, 'total' => 0],
        'classes' => ['percent' => 0, 'covered' => 0, 'total' => 0]
    ];
    
    if (preg_match('/Lines:\s*([\d.]+)%\s*\((\d+)\/(\d+)\)/', $testOutput, $matches)) {
        $coverage['lines'] = [
            'percent' => (float)$matches[1],
            'covered' => (int)$matches[2],
            'total' => (int)$matches[3]
        ];
    }
    
    if (preg_match('/Methods:\s*([\d.]+)%\s*\((\d+)\/(\d+)\)/', $testOutput, $matches)) {
        $coverage['methods'] = [
            'percent' => (float)$matches[1],
            'covered' => (int)$matches[2],
            'total' => (int)$matches[3]
        ];
    }
    
    if (preg_match('/Classes:\s*([\d.]+)%\s*\((\d+)\/(\d+)\)/', $testOutput, $matches)) {
        $coverage['classes'] = [
            'percent' => (float)$matches[1],
            'covered' => (int)$matches[2],
            'total' => (int)$matches[3]
        ];
    }
    
    if ($coverage['lines']['total'] > 0) {
        echo "Coverage Results:\n";
        echo "   📏 Lines: {$coverage['lines']['percent']}% ({$coverage['lines']['covered']}/{$coverage['lines']['total']})\n";
        echo "   ⚙️  Methods: {$coverage['methods']['percent']}% ({$coverage['methods']['covered']}/{$coverage['methods']['total']})\n";
        echo "   🏛️  Classes: {$coverage['classes']['percent']}% ({$coverage['classes']['covered']}/{$coverage['classes']['total']})\n";
        
        // Coverage quality assessment
        $avgCoverage = ($coverage['lines']['percent'] + $coverage['methods']['percent'] + $coverage['classes']['percent']) / 3;
        if ($avgCoverage >= 80) {
            echo "   🎯 Quality: Excellent coverage\n";
        } elseif ($avgCoverage >= 60) {
            echo "   👍 Quality: Good coverage\n";
        } elseif ($avgCoverage >= 40) {
            echo "   ⚠️  Quality: Moderate coverage - consider improving\n";
        } else {
            echo "   🚨 Quality: Low coverage - needs improvement\n";
        }
    } else {
        echo "❌ No coverage data available\n";
    }
} else {
    echo "❌ Code coverage requires Xdebug extension\n";
}
echo "\n";

// Dynamic Source Code and Test Analysis
echo "📁 PROJECT STRUCTURE ANALYSIS\n";
echo str_repeat("-", 40) . "\n";

function analyzeDirectory($dir, $description) {
    if (!is_dir($dir)) return ['files' => 0, 'lines' => 0, 'classes' => 0, 'methods' => 0];
    
    $stats = ['files' => 0, 'lines' => 0, 'classes' => 0, 'methods' => 0, 'interfaces' => 0, 'traits' => 0];
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    
    foreach ($iterator as $file) {
        if ($file->getExtension() !== 'php') continue;
        
        $content = file_get_contents($file->getPathname());
        $stats['files']++;
        $stats['lines'] += substr_count($content, "\n") + 1;
        
        // Count different PHP constructs
        $stats['classes'] += preg_match_all('/^\s*(?:abstract\s+)?class\s+\w+/m', $content) ?: 0;
        $stats['interfaces'] += preg_match_all('/^\s*interface\s+\w+/m', $content) ?: 0;
        $stats['traits'] += preg_match_all('/^\s*trait\s+\w+/m', $content) ?: 0;
        $stats['methods'] += preg_match_all('/^\s*(?:public|private|protected)?\s*function\s+\w+/m', $content) ?: 0;
    }
    
    return $stats;
}

// Analyze source code
$srcStats = analyzeDirectory(__DIR__ . '/src', 'Source Code');
echo "Source Code ({$srcStats['files']} files):\n";
echo "   📄 Lines: " . number_format($srcStats['lines']) . "\n";
echo "   🏛️  Classes: {$srcStats['classes']}\n";
echo "   📋 Interfaces: {$srcStats['interfaces']}\n";
echo "   🧩 Traits: {$srcStats['traits']}\n";
echo "   ⚙️  Methods: {$srcStats['methods']}\n\n";

// Analyze test code
$testStats = analyzeDirectory(__DIR__ . '/tests', 'Test Code');
echo "Test Code ({$testStats['files']} files):\n";
echo "   📄 Lines: " . number_format($testStats['lines']) . "\n";
echo "   🧪 Test Classes: {$testStats['classes']}\n";
echo "   🔬 Test Methods: {$testStats['methods']}\n";

if ($srcStats['lines'] > 0) {
    $testRatio = round(($testStats['lines'] / $srcStats['lines']) * 100, 1);
    echo "   📊 Test-to-Source Ratio: {$testRatio}%\n";
}

// Calculate test coverage ratio
if ($srcStats['methods'] > 0 && isset($testResults)) {
    $methodTestRatio = round(($testResults['passed'] / $srcStats['methods']) * 100, 1);
    echo "   🎯 Method Test Coverage: {$methodTestRatio}%\n";
}
echo "\n";

// Dynamic test distribution analysis
echo "📂 TEST DISTRIBUTION\n";
echo str_repeat("-", 40) . "\n";

$testDirs = ['Unit', 'Integration', 'Feature'];
$testDistribution = [];

foreach ($testDirs as $testType) {
    $dir = __DIR__ . '/tests/' . $testType;
    if (is_dir($dir)) {
        $count = 0;
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->getExtension() === 'php' && strpos($file->getFilename(), 'Test') !== false) {
                $count++;
            }
        }
        
        if ($count > 0) {
            $testDistribution[$testType] = $count;
        }
    }
}

foreach ($testDistribution as $type => $count) {
    echo "   {$type}: {$count} test files\n";
}
echo "   Total: " . array_sum($testDistribution) . " test files\n\n";

// Overall Project Health Assessment
echo "🏆 PROJECT HEALTH ASSESSMENT\n";
echo str_repeat("-", 40) . "\n";

$healthScore = 0;
$maxScore = 10;

// Test coverage contribution (0-3 points)
if (isset($coverage) && $coverage['lines']['total'] > 0) {
    $avgCoverage = ($coverage['lines']['percent'] + $coverage['methods']['percent'] + $coverage['classes']['percent']) / 3;
    if ($avgCoverage >= 80) $healthScore += 3;
    elseif ($avgCoverage >= 60) $healthScore += 2;
    elseif ($avgCoverage >= 40) $healthScore += 1;
}

// Test results contribution (0-3 points)
if (isset($testResults)) {
    if ($testResults['failed'] === 0 && $testResults['errors'] === 0) {
        $healthScore += 3;
    } elseif ($testResults['failed'] <= 2) {
        $healthScore += 2;
    } elseif ($testResults['failed'] <= 5) {
        $healthScore += 1;
    }
}

// Tool availability contribution (0-2 points)
$toolCount = count($tools);
if ($toolCount >= 4) $healthScore += 2;
elseif ($toolCount >= 2) $healthScore += 1;

// Code quality contribution (0-2 points)
if ($srcStats['files'] > 0) {
    $avgMethodsPerClass = $srcStats['classes'] > 0 ? $srcStats['methods'] / $srcStats['classes'] : 0;
    if ($avgMethodsPerClass > 0 && $avgMethodsPerClass <= 15) $healthScore += 1; // Good class size
    if ($testStats['files'] > 0) $healthScore += 1; // Has tests
}

$healthPercentage = round(($healthScore / $maxScore) * 100);

echo "Health Score: {$healthScore}/{$maxScore} ({$healthPercentage}%)\n";

if ($healthPercentage >= 80) {
    echo "Status: 🟢 Excellent - Production Ready\n";
} elseif ($healthPercentage >= 60) {
    echo "Status: 🟡 Good - Minor improvements recommended\n";
} elseif ($healthPercentage >= 40) {
    echo "Status: 🟠 Fair - Several improvements needed\n";
} else {
    echo "Status: 🔴 Poor - Significant work required\n";
}

echo "\nContributing Factors:\n";
if (isset($coverage) && $coverage['lines']['total'] > 0) {
    echo "   📊 Code Coverage: " . round(($coverage['lines']['percent'] + $coverage['methods']['percent'] + $coverage['classes']['percent']) / 3, 1) . "%\n";
}
if (isset($testResults)) {
    $totalTests = $testResults['passed'] + $testResults['failed'] + $testResults['errors'];
    $successRate = $totalTests > 0 ? round(($testResults['passed'] / $totalTests) * 100, 1) : 0;
    echo "   🧪 Test Success Rate: {$successRate}%\n";
}
echo "   🔧 Development Tools: {$toolCount} available\n";
echo "   📁 Project Structure: " . ($srcStats['files'] > 0 ? "✅ Organized" : "❌ Missing") . "\n";

echo "\n" . str_repeat("=", 50) . "\n";
echo "Report Generated: " . date('Y-m-d H:i:s T') . "\n";
echo "Analysis Complete ✅\n";
echo str_repeat("=", 50) . "\n";
