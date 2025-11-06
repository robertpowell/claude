# UK IP Filtering for Website Tracking

A PHP solution that filters website visitor IP addresses to only process UK-based traffic before calling the UserStack API. This helps reduce API costs by avoiding unnecessary calls for non-UK visitors.

## Features

- **Free IP Geolocation**: Uses free services (ip-api.com) with fallback options
- **UK IP Filtering**: Only processes UK-based IPs before calling UserStack
- **Caching**: Built-in 24-hour cache to reduce API calls
- **Dual-Service Fallback**: Automatically falls back to ipapi.co if primary service fails
- **Comprehensive Logging**: Tracks all requests and decisions
- **CORS Support**: Ready for AJAX requests from web pages

## How It Works

1. Visitor hits your website → track.php receives the request
2. Script extracts the visitor's IP address
3. **IP is checked against free geolocation service** (ip-api.com)
4. If IP is **not UK-based**: Request is rejected, no UserStack call made ✅ **Cost saved!**
5. If IP **is UK-based**: Proceeds to call UserStack API for detailed user agent info

## Installation

### Requirements

- PHP 7.4 or higher
- cURL extension enabled
- Write permissions for cache and logs directories

### Setup

1. Clone or download this repository

2. Set your UserStack API key (choose one method):

   **Option A: Environment Variable (Recommended)**
   ```bash
   export USERSTACK_API_KEY='your_api_key_here'
   ```

   **Option B: Edit track.php**
   ```php
   define('USERSTACK_API_KEY', 'your_api_key_here');
   ```

3. Ensure proper permissions:
   ```bash
   chmod 755 *.php
   mkdir -p cache logs
   chmod 755 cache logs
   ```

4. Point your web server to serve track.php

## Usage

### Basic Implementation

Call the tracking script from your website:

```html
<script>
fetch('https://yourdomain.com/track.php')
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      console.log('UK visitor tracked:', data);
    } else {
      console.log('Non-UK visitor or error:', data.message);
    }
  });
</script>
```

### Response Format

**UK IP Response:**
```json
{
  "success": true,
  "message": "UK visitor tracked successfully",
  "data": {
    "ip": "81.2.69.142",
    "is_uk": true,
    "location": {
      "country": "United Kingdom",
      "countryCode": "GB",
      "region": "England",
      "city": "London",
      "timezone": "Europe/London",
      "isp": "British Telecom"
    },
    "userstack": {
      "browser": {
        "name": "Chrome",
        "version": "120.0"
      },
      "os": {
        "name": "Windows",
        "version": "10"
      },
      "device": {
        "type": "desktop"
      }
    }
  }
}
```

**Non-UK IP Response:**
```json
{
  "success": false,
  "message": "Non-UK IP address - tracking skipped",
  "data": {
    "ip": "8.8.8.8",
    "is_uk": false
  }
}
```

## API Services Used

### Primary: ip-api.com
- **Cost**: Free
- **API Key**: Not required
- **Rate Limit**: 45 requests/minute
- **Accuracy**: Very good
- **More Info**: https://ip-api.com/

### Fallback: ipapi.co
- **Cost**: Free tier (1000 requests/day)
- **API Key**: Not required for free tier
- **Rate Limit**: 1000/day, 30,000/month
- **More Info**: https://ipapi.co/

## Caching

The system automatically caches IP geolocation results for 24 hours to:
- Reduce API calls to free services
- Improve response times
- Respect rate limits

Cache files are stored in `cache/` directory and automatically managed.

### Manual Cache Management

Clear expired cache files:
```php
require_once 'IPGeolocation.php';
$cleared = IPGeolocation::clearExpiredCache();
echo "Cleared {$cleared} expired cache files\n";
```

## Testing

Use the included test script to verify functionality:

```bash
php test.php 81.2.69.142    # Test UK IP
php test.php 8.8.8.8        # Test non-UK IP
```

Or test the web endpoint:

```bash
curl http://localhost/track.php
```

## Configuration Options

Edit `track.php` to customize:

```php
// UserStack API Key
define('USERSTACK_API_KEY', 'your_key');

// Log file location
define('LOG_FILE', __DIR__ . '/logs/tracking.log');

// Enable/disable logging
define('ENABLE_LOGGING', true);
```

Edit `IPGeolocation.php` to customize:

```php
// Cache TTL (default: 24 hours)
private const CACHE_TTL = 86400;

// Cache directory
private const CACHE_DIR = __DIR__ . '/cache';
```

## Cost Savings Example

**Without UK Filtering:**
- 1000 visitors/day
- All call UserStack
- Cost: 1000 UserStack API calls/day

**With UK Filtering (assuming 80% non-UK traffic):**
- 1000 visitors/day
- 800 non-UK IPs filtered out (free ip-api.com check)
- 200 UK IPs call UserStack
- Cost: 200 UserStack API calls/day
- **Savings: 80% reduction in UserStack API calls!**

## Logs

All activity is logged to `logs/tracking.log`:

```
[2025-11-06 12:30:45] [INFO] Processing request from IP: 81.2.69.142
[2025-11-06 12:30:46] [INFO] UK IP detected: 81.2.69.142 - proceeding with UserStack
[2025-11-06 12:30:47] [INFO] UserStack data retrieved for UK IP: 81.2.69.142
```

## Troubleshooting

### Issue: "Unable to determine IP address"
**Solution**: Check your web server configuration. You may be behind a proxy/load balancer that uses different headers.

### Issue: Rate limit exceeded
**Solution**: The caching system should prevent this, but if you have very high traffic, consider:
- Increasing cache TTL
- Using a local GeoIP database (e.g., MaxMind GeoLite2)
- Implementing additional caching layers

### Issue: UserStack API errors
**Solution**:
- Verify your API key is correct
- Check your UserStack account limits
- Review logs in `logs/tracking.log`

## Advanced: Using Local GeoIP Database

For high-traffic sites, consider switching to MaxMind's free GeoLite2 database to eliminate external API calls for IP geolocation:

1. Download GeoLite2 Country database
2. Install MaxMind PHP extension or use their PHP library
3. Modify `IPGeolocation.php` to query the local database

This provides unlimited lookups with no rate limits.

## Security Notes

- The script includes validation to reject private/reserved IP ranges
- CORS is enabled by default - restrict this in production if needed
- Store API keys in environment variables, not in code
- Ensure cache and logs directories are not web-accessible

## License

MIT License - feel free to use and modify as needed.

## Support

For issues or questions, please open an issue in the repository.
