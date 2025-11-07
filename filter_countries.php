#!/usr/bin/env php
<?php
/**
 * Filter Countries - Exclude US, UK, and EU countries
 *
 * Shows only countries that are NOT:
 * - United States (US)
 * - United Kingdom (GB)
 * - European Union member states
 */

// ISO 3166-1 alpha-2 country codes to country names
$countries = [
    'AD' => 'Andorra',
    'AE' => 'United Arab Emirates',
    'AF' => 'Afghanistan',
    'AL' => 'Albania',
    'AQ' => 'Antarctica',
    'AR' => 'Argentina',
    'AT' => 'Austria',
    'AU' => 'Australia',
    'AX' => 'Åland Islands',
    'AZ' => 'Azerbaijan',
    'BB' => 'Barbados',
    'BD' => 'Bangladesh',
    'BE' => 'Belgium',
    'BG' => 'Bulgaria',
    'BO' => 'Bolivia',
    'BR' => 'Brazil',
    'BS' => 'Bahamas',
    'BY' => 'Belarus',
    'BZ' => 'Belize',
    'CA' => 'Canada',
    'CH' => 'Switzerland',
    'CL' => 'Chile',
    'CN' => 'China',
    'CO' => 'Colombia',
    'CZ' => 'Czech Republic',
    'DE' => 'Germany',
    'DK' => 'Denmark',
    'DO' => 'Dominican Republic',
    'DZ' => 'Algeria',
    'EC' => 'Ecuador',
    'EE' => 'Estonia',
    'EG' => 'Egypt',
    'ES' => 'Spain',
    'ET' => 'Ethiopia',
    'FI' => 'Finland',
    'FR' => 'France',
    'GA' => 'Gabon',
    'GB' => 'United Kingdom',
    'GE' => 'Georgia',
    'GH' => 'Ghana',
    'GL' => 'Greenland',
    'GR' => 'Greece',
    'HK' => 'Hong Kong',
    'HR' => 'Croatia',
    'HU' => 'Hungary',
    'ID' => 'Indonesia',
    'IE' => 'Ireland',
    'IL' => 'Israel',
    'IN' => 'India',
    'IR' => 'Iran',
    'IS' => 'Iceland',
    'IT' => 'Italy',
    'JP' => 'Japan',
    'KE' => 'Kenya',
    'KH' => 'Cambodia',
    'KN' => 'Saint Kitts and Nevis',
    'KR' => 'South Korea',
    'KW' => 'Kuwait',
    'KZ' => 'Kazakhstan',
    'LA' => 'Laos',
    'LK' => 'Sri Lanka',
    'LT' => 'Lithuania',
    'LU' => 'Luxembourg',
    'LV' => 'Latvia',
    'MA' => 'Morocco',
    'MD' => 'Moldova',
    'ME' => 'Montenegro',
    'MK' => 'North Macedonia',
    'MN' => 'Mongolia',
    'MT' => 'Malta',
    'MX' => 'Mexico',
    'MY' => 'Malaysia',
    'NG' => 'Nigeria',
    'NI' => 'Nicaragua',
    'NL' => 'Netherlands',
    'NO' => 'Norway',
    'NZ' => 'New Zealand',
    'OM' => 'Oman',
    'PA' => 'Panama',
    'PH' => 'Philippines',
    'PK' => 'Pakistan',
    'PL' => 'Poland',
    'PR' => 'Puerto Rico',
    'PT' => 'Portugal',
    'RO' => 'Romania',
    'RS' => 'Serbia',
    'RU' => 'Russia',
    'SA' => 'Saudi Arabia',
    'SC' => 'Seychelles',
    'SE' => 'Sweden',
    'SG' => 'Singapore',
    'SI' => 'Slovenia',
    'SK' => 'Slovakia',
    'SL' => 'Sierra Leone',
    'SO' => 'Somalia',
    'TH' => 'Thailand',
    'TN' => 'Tunisia',
    'TR' => 'Turkey',
    'TW' => 'Taiwan',
    'UA' => 'Ukraine',
    'US' => 'United States',
    'UY' => 'Uruguay',
    'VE' => 'Venezuela',
    'VN' => 'Vietnam',
    'ZA' => 'South Africa',
    'ZW' => 'Zimbabwe',
];

// European Union member states (27 countries as of 2024)
$euCountries = [
    'AT', // Austria
    'BE', // Belgium
    'BG', // Bulgaria
    'HR', // Croatia
    'CY', // Cyprus
    'CZ', // Czech Republic
    'DK', // Denmark
    'EE', // Estonia
    'FI', // Finland
    'FR', // France
    'DE', // Germany
    'GR', // Greece
    'HU', // Hungary
    'IE', // Ireland
    'IT', // Italy
    'LV', // Latvia
    'LT', // Lithuania
    'LU', // Luxembourg
    'MT', // Malta
    'NL', // Netherlands
    'PL', // Poland
    'PT', // Portugal
    'RO', // Romania
    'SK', // Slovakia
    'SI', // Slovenia
    'ES', // Spain
    'SE', // Sweden
];

// Countries to exclude
$excludeCountries = array_merge(
    ['US', 'GB'], // US and UK
    $euCountries  // EU countries
);

// Your country codes
$yourCodes = [
    'US', 'NL', 'DE', 'CN', 'GB', 'FR', 'SG', 'JP', 'CA', 'RU',
    'AU', 'FI', 'PL', 'BE', 'HK', 'KR', 'BR', 'IN', 'SE', 'IE',
    'VN', 'AD', 'ID', 'TR', 'CH', 'LT', 'NO', 'TH', 'ES', 'SC',
    'AE', 'UA', 'MN', 'RO', 'IT', 'AX', 'ZA', 'BG', 'MD', 'NG',
    'IL', 'HU', 'IR', 'MY', 'AT', 'ZW', 'MX', 'BZ', 'KZ', 'HR',
    'PT', 'KH', 'MA', 'CZ', 'LV', 'PH', 'LU', 'AQ', 'TW', 'DK',
    'BD', 'EE', 'CL', 'AR', 'BY', 'MK', 'SK', 'GE', 'MT', 'EG',
    'VE', 'GH', 'AL', 'BO', 'LK', 'SI', 'GR', 'KN', 'GL', 'EC',
    'IS', 'NZ', 'UY', 'KE', 'TN', 'ET', 'NI', 'RS', 'DZ', 'BB',
    'PK', 'PR', 'PA', 'CO', 'KW', 'DO', 'LA', 'SL', 'BS', 'AF',
    'OM', 'SA', 'SO', 'AZ', 'GA', 'ME'
];

// Remove duplicates and sort
$yourCodes = array_unique($yourCodes);
sort($yourCodes);

// Filter out US, UK, and EU countries
$filteredCodes = array_filter($yourCodes, function($code) use ($excludeCountries) {
    return !in_array($code, $excludeCountries);
});

// Separate excluded countries for reporting
$excludedCodes = array_filter($yourCodes, function($code) use ($excludeCountries) {
    return in_array($code, $excludeCountries);
});

echo "Countries Filtered (Excluding US, UK, and EU)\n";
echo str_repeat("=", 60) . "\n\n";

echo "FILTERED COUNTRIES (NOT US, UK, or EU):\n";
echo str_repeat("-", 60) . "\n";
printf("%-6s | %s\n", "Code", "Country Name");
echo str_repeat("-", 60) . "\n";

foreach ($filteredCodes as $code) {
    $countryName = $countries[$code] ?? 'Unknown';
    printf("%-6s | %s\n", $code, $countryName);
}

echo "\n" . str_repeat("=", 60) . "\n\n";

echo "EXCLUDED COUNTRIES (US, UK, EU):\n";
echo str_repeat("-", 60) . "\n";
printf("%-6s | %-30s | %s\n", "Code", "Country Name", "Reason");
echo str_repeat("-", 60) . "\n";

foreach ($excludedCodes as $code) {
    $countryName = $countries[$code] ?? 'Unknown';
    $reason = '';
    if ($code === 'US') {
        $reason = 'United States';
    } elseif ($code === 'GB') {
        $reason = 'United Kingdom';
    } elseif (in_array($code, $euCountries)) {
        $reason = 'EU Member';
    }
    printf("%-6s | %-30s | %s\n", $code, $countryName, $reason);
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "Summary:\n";
echo "  Total countries in list: " . count($yourCodes) . "\n";
echo "  Filtered (non-US/UK/EU): " . count($filteredCodes) . "\n";
echo "  Excluded (US/UK/EU): " . count($excludedCodes) . "\n";

$percentFiltered = round(count($filteredCodes) / count($yourCodes) * 100, 2);
echo "\n  Percentage of traffic from non-US/UK/EU: {$percentFiltered}%\n";
