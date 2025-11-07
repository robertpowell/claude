<?php
/**
 * Integration Example - How to use check_allowed_ip.php in your track.php
 *
 * This shows how to filter for allowed countries (US, UK, EU + premium countries)
 * before calling UserStack API
 */

// Include the allowed country checker
require_once 'check_allowed_ip.php';

// Get the visitor's IP address
$remoteaddr = $_SERVER['REMOTE_ADDR'];

// Check if IP is from allowed country (returns 1 for allowed, 0 for not allowed)
$isAllowed = checkAllowedIP($remoteaddr);

// Only proceed with UserStack if from allowed country
if ($isAllowed == 1) {
    echo "Allowed country IP detected: {$remoteaddr}\n";
    echo "Proceeding with UserStack API call...\n";

    // YOUR EXISTING USERSTACK CODE GOES HERE
    // Example:
    // $userAgent = $_SERVER['HTTP_USER_AGENT'];
    // $userstackData = callUserStack($userAgent);
    // ... rest of your code ...

} else {
    echo "Non-allowed country IP detected: {$remoteaddr}\n";
    echo "Skipping UserStack API call (cost saved!)\n";

    // Optionally log or return a response
    // Example:
    // header('Content-Type: application/json');
    // echo json_encode(['success' => false, 'message' => 'Country not in allowed list']);
}

/**
 * Example UserStack API call function
 */
function callUserStack($userAgent) {
    $apiKey = 'your_api_key_here';
    $url = sprintf(
        'http://api.userstack.com/detect?access_key=%s&ua=%s',
        urlencode($apiKey),
        urlencode($userAgent)
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

/**
 * ALLOWED COUNTRIES (35 total):
 *
 * EU Members (27):
 * AT, BE, BG, CZ, DE, DK, EE, ES, FI, FR, GB, GR, HR, HU, IE, IT,
 * LT, LU, LV, MT, NL, PL, PT, RO, SE, SI, SK
 *
 * Other Premium Countries (8):
 * US (United States)
 * CA (Canada)
 * CH (Switzerland)
 * AU (Australia)
 * NO (Norway)
 * NZ (New Zealand)
 * IS (Iceland)
 * JP (Japan)
 */
