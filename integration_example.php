<?php
/**
 * Integration Example - How to add UK IP filtering to your existing track.php
 *
 * This shows how to integrate the UK IP checker into your existing UserStack script
 */

// Include the UK IP checker
require_once 'check_uk_ip.php';

// Get the visitor's IP address
$remoteaddr = $_SERVER['REMOTE_ADDR'];

// Check if UK IP (returns 1 for UK, 0 for non-UK)
$isUK = checkUKIP($remoteaddr);

// Only proceed with UserStack if UK IP
if ($isUK == 1) {
    echo "UK IP detected: {$remoteaddr}\n";
    echo "Proceeding with UserStack API call...\n";

    // YOUR EXISTING USERSTACK CODE GOES HERE
    // Example:
    // $userAgent = $_SERVER['HTTP_USER_AGENT'];
    // $userstackData = callUserStack($userAgent);
    // ... rest of your code ...

} else {
    echo "Non-UK IP detected: {$remoteaddr}\n";
    echo "Skipping UserStack API call (cost saved!)\n";

    // Optionally log or return a response
    // Example:
    // header('Content-Type: application/json');
    // echo json_encode(['success' => false, 'message' => 'Non-UK IP']);
}

// Example with your existing UserStack function
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
