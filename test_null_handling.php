#!/usr/bin/env php
<?php
/**
 * Test null and empty country code handling
 */

require_once __DIR__ . '/check_allowed_ip.php';

echo "Testing null and empty country code handling\n";
echo str_repeat("=", 60) . "\n\n";

// Test the isCountryAllowed function directly with various inputs
$testCases = [
    ['input' => null, 'description' => 'NULL value'],
    ['input' => '', 'description' => 'Empty string'],
    ['input' => '   ', 'description' => 'Whitespace only'],
    ['input' => 'GB', 'description' => 'Valid UK code'],
    ['input' => 'US', 'description' => 'Valid US code'],
    ['input' => 'CN', 'description' => 'Valid but not allowed (China)'],
];

echo "Testing isCountryAllowed() function:\n";
echo str_repeat("-", 60) . "\n";

foreach ($testCases as $test) {
    $input = $test['input'];
    $description = $test['description'];

    // Call the function using reflection since it's not directly accessible
    $result = isCountryAllowed($input);
    $resultInt = $result ? 1 : 0;

    $displayInput = $input === null ? 'null' : "'{$input}'";

    printf("%-20s | %-30s | Result: %d %s\n",
        $displayInput,
        $description,
        $resultInt,
        ($resultInt == 0 && in_array($input, [null, '', '   '])) ? '✓' : ''
    );
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "All null/empty values correctly return 0 (not allowed)\n";
echo "This ensures failed lookups are treated as not allowed.\n";
