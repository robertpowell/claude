<?php
/**
 * Simple UK IP Checker
 *
 * Usage:
 *   $remoteaddr = $_SERVER['REMOTE_ADDR'];
 *   $isUK = checkUKIP($remoteaddr);
 *   if ($isUK == 1) {
 *       // UK IP - proceed with UserStack
 *   } else {
 *       // Non-UK IP - skip UserStack
 *   }
 */

/**
 * Check if an IP address is from the UK
 *
 * @param string $ip The IP address to check
 * @return int Returns 1 if UK, 0 if not UK
 */
function checkUKIP($ip) {
    // Validate IP address
    if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        return 0;
    }

    // Check cache first
    $cachedResult = getIPCache($ip);
    if ($cachedResult !== null) {
        return $cachedResult;
    }

    // Try primary service: ip-api.com (free, no key required)
    $countryCode = getCountryCode($ip);

    // Check if UK
    $isUK = ($countryCode === 'GB' || $countryCode === 'UK') ? 1 : 0;

    // Cache the result
    saveIPCache($ip, $isUK);

    return $isUK;
}

/**
 * Get country code from ip-api.com
 *
 * @param string $ip The IP address
 * @return string|null Country code or null
 */
function getCountryCode($ip) {
    try {
        $url = "http://ip-api.com/json/{$ip}?fields=status,countryCode";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);
            if ($data && isset($data['status']) && $data['status'] === 'success') {
                return $data['countryCode'] ?? null;
            }
        }
    } catch (Exception $e) {
        error_log("IP lookup failed: " . $e->getMessage());
    }

    return null;
}

/**
 * Get cached IP result
 *
 * @param string $ip The IP address
 * @return int|null Returns 1, 0, or null if not cached
 */
function getIPCache($ip) {
    $cacheDir = __DIR__ . '/cache';
    if (!is_dir($cacheDir)) {
        return null;
    }

    $cacheFile = $cacheDir . '/' . md5($ip) . '.cache';

    if (file_exists($cacheFile)) {
        $cacheData = json_decode(file_get_contents($cacheFile), true);

        if ($cacheData && isset($cacheData['expires']) && $cacheData['expires'] > time()) {
            return (int)$cacheData['isUK'];
        }
    }

    return null;
}

/**
 * Save IP result to cache
 *
 * @param string $ip The IP address
 * @param int $isUK 1 for UK, 0 for non-UK
 */
function saveIPCache($ip, $isUK) {
    $cacheDir = __DIR__ . '/cache';
    if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0755, true);
    }

    $cacheFile = $cacheDir . '/' . md5($ip) . '.cache';
    $cacheData = [
        'ip' => $ip,
        'isUK' => $isUK,
        'expires' => time() + 86400, // 24 hours
        'cached_at' => date('Y-m-d H:i:s')
    ];

    file_put_contents($cacheFile, json_encode($cacheData));
}
