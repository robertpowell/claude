#!/usr/bin/env php
<?php
/**
 * Test Script for UK IP Filtering
 *
 * Usage: php test.php [ip_address]
 * Example: php test.php 81.2.69.142
 */

require_once __DIR__ . '/IPGeolocation.php';

function printHeader(string $text): void
{
    echo "\n" . str_repeat("=", 60) . "\n";
    echo $text . "\n";
    echo str_repeat("=", 60) . "\n\n";
}

function printResult(string $label, $value): void
{
    echo sprintf("%-20s: %s\n", $label, is_bool($value) ? ($value ? 'Yes' : 'No') : $value);
}

// Get IP from command line argument or use default test IPs
$testIP = $argv[1] ?? null;

if ($testIP === null) {
    echo "Usage: php test.php [ip_address]\n\n";
    echo "Testing with default IPs...\n";

    $testIPs = [
        '81.2.69.142'    => 'UK IP (London)',
        '8.8.8.8'        => 'US IP (Google DNS)',
        '1.1.1.1'        => 'Australia IP (Cloudflare)',
        '86.135.22.1'    => 'UK IP',
        '185.201.10.1'   => 'Non-UK European IP',
    ];

    foreach ($testIPs as $ip => $description) {
        printHeader("Testing: {$description} ({$ip})");

        $isUK = IPGeolocation::isUKIP($ip);
        $locationData = IPGeolocation::getLocationData($ip);

        printResult("IP Address", $ip);
        printResult("Is UK IP?", $isUK);

        if ($locationData) {
            printResult("Country", $locationData['country']);
            printResult("Country Code", $locationData['countryCode']);
            printResult("Region", $locationData['region']);
            printResult("City", $locationData['city']);
            printResult("Timezone", $locationData['timezone']);
            printResult("ISP", $locationData['isp']);
        } else {
            echo "Location data: Not available\n";
        }

        if ($isUK) {
            echo "\n✓ Would proceed to call UserStack API\n";
        } else {
            echo "\n✗ Would skip UserStack API call (cost saved!)\n";
        }

        // Small delay to respect rate limits
        sleep(1);
    }

    printHeader("Cache Management");
    $cleared = IPGeolocation::clearExpiredCache();
    echo "Cleared {$cleared} expired cache entries\n";

} else {
    // Test single IP provided as argument
    printHeader("Testing IP: {$testIP}");

    // Validate IP format
    if (!filter_var($testIP, FILTER_VALIDATE_IP)) {
        echo "ERROR: Invalid IP address format\n";
        exit(1);
    }

    // Check if UK
    $startTime = microtime(true);
    $isUK = IPGeolocation::isUKIP($testIP);
    $checkTime = microtime(true) - $startTime;

    printResult("IP Address", $testIP);
    printResult("Is UK IP?", $isUK);
    printResult("Check Time", number_format($checkTime * 1000, 2) . " ms");

    // Get full location data
    echo "\n";
    $startTime = microtime(true);
    $locationData = IPGeolocation::getLocationData($testIP);
    $locationTime = microtime(true) - $startTime;

    if ($locationData) {
        printResult("Country", $locationData['country']);
        printResult("Country Code", $locationData['countryCode']);
        printResult("Region", $locationData['region']);
        printResult("City", $locationData['city']);
        printResult("Zip Code", $locationData['zip']);
        printResult("Latitude", $locationData['lat']);
        printResult("Longitude", $locationData['lon']);
        printResult("Timezone", $locationData['timezone']);
        printResult("ISP", $locationData['isp']);
        printResult("Lookup Time", number_format($locationTime * 1000, 2) . " ms");
    } else {
        echo "Location data: Not available\n";
    }

    echo "\n";
    if ($isUK) {
        echo "✓ Result: This IP would proceed to UserStack API\n";
    } else {
        echo "✗ Result: This IP would be filtered out (UserStack API call saved!)\n";
    }
}

printHeader("Summary");
echo "Testing complete. Check results above.\n";
echo "Run with a specific IP: php test.php <ip_address>\n";
echo "\n";
