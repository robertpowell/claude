#!/usr/bin/env php
<?php
/**
 * Test Script for check_allowed_ip.php
 *
 * Tests if IPs from various countries are allowed
 *
 * Usage: php test_allowed.php [ip_address]
 */

require_once __DIR__ . '/check_allowed_ip.php';

function printHeader(string $text): void
{
    echo "\n" . str_repeat("=", 60) . "\n";
    echo $text . "\n";
    echo str_repeat("=", 60) . "\n\n";
}

// Get IP from command line or use test IPs
$testIP = $argv[1] ?? null;

if ($testIP) {
    // Test single IP
    echo "Testing IP: {$testIP}\n";
    echo str_repeat("-", 50) . "\n";

    $isAllowed = checkAllowedIP($testIP);

    echo "IP Address: {$testIP}\n";
    echo "Result: \$isAllowed = {$isAllowed}\n";

    if ($isAllowed == 1) {
        echo "✓ ALLOWED - Would call UserStack API\n";
    } else {
        echo "✗ NOT ALLOWED - Would skip UserStack API (cost saved!)\n";
    }

} else {
    // Test multiple IPs from different regions
    echo "Testing multiple IPs from different countries...\n";

    $testIPs = [
        // ALLOWED COUNTRIES
        '81.2.69.142'    => ['UK (London)', true],
        '8.8.8.8'        => ['US (Google DNS)', true],
        '185.60.216.35'  => ['Germany', true],
        '2.16.255.1'     => ['France', true],
        '35.189.65.1'    => ['Canada', true],
        '1.1.1.1'        => ['Australia (Cloudflare)', true],
        '160.16.0.1'     => ['Japan', true],
        '185.220.101.1'  => ['Norway', true],
        '130.225.0.1'    => ['Denmark', true],

        // NOT ALLOWED COUNTRIES
        '200.195.141.1'  => ['Brazil', false],
        '189.203.0.1'    => ['Mexico', false],
        '103.28.248.1'   => ['India', false],
        '223.5.5.5'      => ['China (Alibaba DNS)', false],
        '180.76.76.76'   => ['China (Baidu)', false],
        '91.108.56.0'    => ['Russia', false],
        '196.1.95.1'     => ['South Africa', false],
    ];

    printHeader("Testing Allowed vs Not Allowed IPs");

    echo "ALLOWED COUNTRIES:\n";
    echo str_repeat("-", 60) . "\n";

    foreach ($testIPs as $ip => $info) {
        list($description, $shouldBeAllowed) = $info;

        if (!$shouldBeAllowed) continue;

        $startTime = microtime(true);
        $isAllowed = checkAllowedIP($ip);
        $duration = (microtime(true) - $startTime) * 1000;

        printf("%-15s | %-20s | \$isAllowed=%d | %5.1fms | %s\n",
            $ip,
            $description,
            $isAllowed,
            $duration,
            $isAllowed == 1 ? '✓' : '✗'
        );

        sleep(1); // Rate limit protection
    }

    echo "\nNOT ALLOWED COUNTRIES:\n";
    echo str_repeat("-", 60) . "\n";

    foreach ($testIPs as $ip => $info) {
        list($description, $shouldBeAllowed) = $info;

        if ($shouldBeAllowed) continue;

        $startTime = microtime(true);
        $isAllowed = checkAllowedIP($ip);
        $duration = (microtime(true) - $startTime) * 1000;

        printf("%-15s | %-20s | \$isAllowed=%d | %5.1fms | %s\n",
            $ip,
            $description,
            $isAllowed,
            $duration,
            $isAllowed == 0 ? '✓' : '✗'
        );

        sleep(1); // Rate limit protection
    }

    printHeader("Allowed Countries List");
    $allowedCountries = getAllowedCountries();
    echo "Total: " . count($allowedCountries) . " countries\n\n";

    $chunks = array_chunk($allowedCountries, 10);
    foreach ($chunks as $chunk) {
        echo implode(', ', $chunk) . "\n";
    }

    printHeader("Summary");
    echo "Testing complete!\n";
    echo "Run with a specific IP: php test_allowed.php <ip_address>\n";
}

echo "\n";
