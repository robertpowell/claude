<?php
/**
 * Website Tracking Script with UK IP Filtering
 *
 * This script filters requests to only process UK-based IPs before
 * calling UserStack API, saving on API costs.
 */

require_once __DIR__ . '/IPGeolocation.php';

// Configuration
define('USERSTACK_API_KEY', getenv('USERSTACK_API_KEY') ?: 'your_api_key_here');
define('LOG_FILE', __DIR__ . '/logs/tracking.log');
define('ENABLE_LOGGING', true);

/**
 * Get the visitor's IP address
 */
function getVisitorIP(): string
{
    $ip = '';

    // Check various headers for IP (useful behind proxies/load balancers)
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // X-Forwarded-For can contain multiple IPs, get the first one
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ips[0]);
    } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    return $ip;
}

/**
 * Call UserStack API for detailed user agent information
 */
function getUserStackData(string $userAgent, string $ip): ?array
{
    if (USERSTACK_API_KEY === 'your_api_key_here') {
        logMessage("WARNING: UserStack API key not configured", 'WARNING');
        return null;
    }

    try {
        $url = sprintf(
            'http://api.userstack.com/detect?access_key=%s&ua=%s',
            urlencode(USERSTACK_API_KEY),
            urlencode($userAgent)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);

            if (isset($data['error'])) {
                logMessage("UserStack API Error: " . json_encode($data['error']), 'ERROR');
                return null;
            }

            return $data;
        }

        logMessage("UserStack API returned HTTP {$httpCode}", 'ERROR');
    } catch (Exception $e) {
        logMessage("UserStack API call failed: " . $e->getMessage(), 'ERROR');
    }

    return null;
}

/**
 * Log messages to file
 */
function logMessage(string $message, string $level = 'INFO'): void
{
    if (!ENABLE_LOGGING) {
        return;
    }

    $logDir = dirname(LOG_FILE);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }

    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[{$timestamp}] [{$level}] {$message}\n";

    file_put_contents(LOG_FILE, $logEntry, FILE_APPEND);
}

/**
 * Main tracking logic
 */
function processTracking(): array
{
    $response = [
        'success' => false,
        'message' => '',
        'data' => []
    ];

    // Get visitor IP
    $visitorIP = getVisitorIP();

    if (empty($visitorIP)) {
        $response['message'] = 'Unable to determine IP address';
        logMessage("No IP address found", 'WARNING');
        return $response;
    }

    logMessage("Processing request from IP: {$visitorIP}");

    // Check if IP is UK-based (using free service)
    $isUK = IPGeolocation::isUKIP($visitorIP);

    $response['data']['ip'] = $visitorIP;
    $response['data']['is_uk'] = $isUK;

    if (!$isUK) {
        $response['success'] = false;
        $response['message'] = 'Non-UK IP address - tracking skipped';
        logMessage("Skipped non-UK IP: {$visitorIP}", 'INFO');
        return $response;
    }

    logMessage("UK IP detected: {$visitorIP} - proceeding with UserStack", 'INFO');

    // Get additional location data
    $locationData = IPGeolocation::getLocationData($visitorIP);
    if ($locationData) {
        $response['data']['location'] = $locationData;
    }

    // Get user agent
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    if (empty($userAgent)) {
        $response['message'] = 'No user agent provided';
        logMessage("No user agent found for UK IP: {$visitorIP}", 'WARNING');
        return $response;
    }

    // Call UserStack for UK IPs only
    $userStackData = getUserStackData($userAgent, $visitorIP);

    if ($userStackData) {
        $response['success'] = true;
        $response['message'] = 'UK visitor tracked successfully';
        $response['data']['userstack'] = $userStackData;
        logMessage("UserStack data retrieved for UK IP: {$visitorIP}", 'INFO');
    } else {
        $response['message'] = 'Failed to retrieve UserStack data';
        logMessage("Failed to retrieve UserStack data for UK IP: {$visitorIP}", 'ERROR');
    }

    return $response;
}

/**
 * Handle AJAX/API requests
 */
function handleRequest(): void
{
    // Set headers for JSON response
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST');

    // Process the tracking request
    $result = processTracking();

    // Return JSON response
    echo json_encode($result, JSON_PRETTY_PRINT);

    // Log the response
    logMessage("Response: " . json_encode($result));
}

// Execute the tracking
try {
    handleRequest();
} catch (Exception $e) {
    logMessage("Fatal error: " . $e->getMessage(), 'ERROR');
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error',
        'error' => $e->getMessage()
    ]);
}
