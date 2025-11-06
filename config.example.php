<?php
/**
 * Configuration Example
 *
 * Copy this file to config.php and customize for your environment
 */

// UserStack API Configuration
define('USERSTACK_API_KEY', getenv('USERSTACK_API_KEY') ?: 'your_userstack_api_key_here');

// Logging Configuration
define('ENABLE_LOGGING', true);
define('LOG_FILE', __DIR__ . '/logs/tracking.log');
define('LOG_LEVEL', 'INFO'); // DEBUG, INFO, WARNING, ERROR

// Cache Configuration
define('ENABLE_CACHE', true);
define('CACHE_TTL', 86400); // 24 hours in seconds

// IP Detection Configuration
// Trusted proxy headers (if behind load balancer/CDN)
define('TRUSTED_PROXIES', [
    // Add your proxy/load balancer IPs here
    // '10.0.0.1',
    // '172.16.0.1',
]);

// Countries to process (ISO country codes)
define('ALLOWED_COUNTRIES', [
    'GB', // United Kingdom
    'UK', // Alternative UK code
]);

// Rate Limiting (optional)
define('ENABLE_RATE_LIMITING', false);
define('RATE_LIMIT_REQUESTS', 100); // Max requests per window
define('RATE_LIMIT_WINDOW', 60); // Time window in seconds

// CORS Configuration
define('CORS_ALLOW_ORIGIN', '*'); // Change to your domain in production
define('CORS_ALLOW_METHODS', 'GET, POST, OPTIONS');
define('CORS_ALLOW_HEADERS', 'Content-Type, Authorization');

// Debug Mode
define('DEBUG_MODE', false); // Set to true for detailed error messages
