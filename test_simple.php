#!/usr/bin/env php
<?php
/**
 * Simple Test Script for check_uk_ip.php
 *
 * Usage: php test_simple.php [ip_address]
 */

require_once __DIR__ . '/check_uk_ip.php';

// Get IP from command line or use test IPs
$testIP = $argv[1] ?? null;

if ($testIP) {
    // Test single IP
    echo "Testing IP: {$testIP}\n";
    echo str_repeat("-", 50) . "\n";

    $isUK = checkUKIP($testIP);

    echo "IP Address: {$testIP}\n";
    echo "Result: \$isUK = {$isUK}\n";

    if ($isUK == 1) {
        echo "✓ UK IP - Would call UserStack API\n";
    } else {
        echo "✗ Non-UK IP - Would skip UserStack API (cost saved!)\n";
    }

} else {
    // Test multiple IPs
    echo "Testing multiple IPs...\n\n";

    $testIPs = [
        '81.2.69.142'    => 'UK IP (London)',
        '86.135.22.1'    => 'UK IP',
        '8.8.8.8'        => 'US IP (Google DNS)',
        '1.1.1.1'        => 'Australia IP (Cloudflare)',
        '185.201.10.1'   => 'European IP',
    ];

    foreach ($testIPs as $ip => $description) {
        echo str_repeat("=", 50) . "\n";
        echo "{$description}\n";
        echo str_repeat("-", 50) . "\n";

        $startTime = microtime(true);
        $isUK = checkUKIP($ip);
        $duration = (microtime(true) - $startTime) * 1000;

        echo "IP: {$ip}\n";
        echo "\$isUK = {$isUK}\n";
        echo "Time: " . number_format($duration, 2) . " ms\n";

        if ($isUK == 1) {
            echo "Status: ✓ Would call UserStack\n";
        } else {
            echo "Status: ✗ Would skip UserStack (saved!)\n";
        }

        echo "\n";

        // Small delay to respect rate limits
        sleep(1);
    }

    echo str_repeat("=", 50) . "\n";
    echo "Testing complete!\n";
    echo "\nUsage: php test_simple.php [ip_address]\n";
}
