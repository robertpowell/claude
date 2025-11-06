<?php
/**
 * IP Geolocation Helper Class
 *
 * Uses free services to determine IP location without requiring API keys.
 * Supports multiple providers with fallback options.
 */
class IPGeolocation
{
    private const CACHE_DIR = __DIR__ . '/cache';
    private const CACHE_TTL = 86400; // 24 hours

    /**
     * Check if an IP address is from the UK
     *
     * @param string $ip The IP address to check
     * @param bool $useCache Whether to use caching (default: true)
     * @return bool True if UK-based, false otherwise
     */
    public static function isUKIP(string $ip): bool
    {
        // Validate IP address
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return false;
        }

        // Check cache first
        $cachedResult = self::getFromCache($ip);
        if ($cachedResult !== null) {
            return $cachedResult;
        }

        // Try primary service: ip-api.com (free, no key required)
        $countryCode = self::getCountryFromIPAPI($ip);

        // Fallback to ipapi.co if primary fails
        if ($countryCode === null) {
            $countryCode = self::getCountryFromIPAPICO($ip);
        }

        $isUK = ($countryCode === 'GB' || $countryCode === 'UK');

        // Cache the result
        self::saveToCache($ip, $isUK);

        return $isUK;
    }

    /**
     * Get full location data for an IP
     *
     * @param string $ip The IP address to lookup
     * @return array|null Location data or null on failure
     */
    public static function getLocationData(string $ip): ?array
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return null;
        }

        $data = self::fetchFromIPAPI($ip);

        if ($data && isset($data['status']) && $data['status'] === 'success') {
            return [
                'country' => $data['country'] ?? null,
                'countryCode' => $data['countryCode'] ?? null,
                'region' => $data['regionName'] ?? null,
                'city' => $data['city'] ?? null,
                'zip' => $data['zip'] ?? null,
                'lat' => $data['lat'] ?? null,
                'lon' => $data['lon'] ?? null,
                'timezone' => $data['timezone'] ?? null,
                'isp' => $data['isp'] ?? null,
            ];
        }

        return null;
    }

    /**
     * Fetch data from ip-api.com
     * Free service, 45 requests per minute limit
     */
    private static function fetchFromIPAPI(string $ip): ?array
    {
        try {
            $url = "http://ip-api.com/json/{$ip}?fields=status,message,country,countryCode,region,regionName,city,zip,lat,lon,timezone,isp";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                return json_decode($response, true);
            }
        } catch (Exception $e) {
            error_log("IP-API lookup failed: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Get country code from ip-api.com
     */
    private static function getCountryFromIPAPI(string $ip): ?string
    {
        $data = self::fetchFromIPAPI($ip);

        if ($data && isset($data['status']) && $data['status'] === 'success') {
            return $data['countryCode'] ?? null;
        }

        return null;
    }

    /**
     * Fallback: Get country code from ipapi.co
     * Free tier: 1000 requests per day
     */
    private static function getCountryFromIPAPICO(string $ip): ?string
    {
        try {
            $url = "https://ipapi.co/{$ip}/country/";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
            curl_setopt($ch, CURLOPT_USERAGENT, 'curl/7.0');

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                return trim($response);
            }
        } catch (Exception $e) {
            error_log("IPAPI.CO lookup failed: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Get cached result for an IP
     */
    private static function getFromCache(string $ip): ?bool
    {
        if (!is_dir(self::CACHE_DIR)) {
            return null;
        }

        $cacheFile = self::CACHE_DIR . '/' . md5($ip) . '.cache';

        if (file_exists($cacheFile)) {
            $cacheData = json_decode(file_get_contents($cacheFile), true);

            if ($cacheData && isset($cacheData['expires']) && $cacheData['expires'] > time()) {
                return (bool)$cacheData['isUK'];
            }
        }

        return null;
    }

    /**
     * Save result to cache
     */
    private static function saveToCache(string $ip, bool $isUK): void
    {
        if (!is_dir(self::CACHE_DIR)) {
            mkdir(self::CACHE_DIR, 0755, true);
        }

        $cacheFile = self::CACHE_DIR . '/' . md5($ip) . '.cache';
        $cacheData = [
            'ip' => $ip,
            'isUK' => $isUK,
            'expires' => time() + self::CACHE_TTL,
            'cached_at' => date('Y-m-d H:i:s')
        ];

        file_put_contents($cacheFile, json_encode($cacheData));
    }

    /**
     * Clear expired cache files
     */
    public static function clearExpiredCache(): int
    {
        if (!is_dir(self::CACHE_DIR)) {
            return 0;
        }

        $cleared = 0;
        $files = glob(self::CACHE_DIR . '/*.cache');

        foreach ($files as $file) {
            $cacheData = json_decode(file_get_contents($file), true);

            if (!$cacheData || !isset($cacheData['expires']) || $cacheData['expires'] < time()) {
                unlink($file);
                $cleared++;
            }
        }

        return $cleared;
    }
}
