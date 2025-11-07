#!/usr/bin/env php
<?php
/**
 * Country Code Lookup Script
 *
 * Displays country names for ISO 3166-1 alpha-2 country codes
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

echo "Country Code Lookup\n";
echo str_repeat("=", 60) . "\n\n";

printf("%-6s | %s\n", "Code", "Country Name");
echo str_repeat("-", 60) . "\n";

foreach ($yourCodes as $code) {
    $countryName = $countries[$code] ?? 'Unknown';
    printf("%-6s | %s\n", $code, $countryName);
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "Total countries: " . count($yourCodes) . "\n";

// Count by region (simple categorization)
$ukOnly = array_filter($yourCodes, fn($c) => $c === 'GB');
$nonUK = array_filter($yourCodes, fn($c) => $c !== 'GB');

echo "\nBreakdown:\n";
echo "  UK (GB): " . count($ukOnly) . "\n";
echo "  Non-UK: " . count($nonUK) . "\n";

if (count($ukOnly) > 0) {
    $percentUK = round(count($ukOnly) / count($yourCodes) * 100, 2);
    echo "\nIf these represent your traffic:\n";
    echo "  UK traffic: {$percentUK}%\n";
    echo "  Non-UK traffic: " . (100 - $percentUK) . "%\n";
    echo "  Potential UserStack API savings: ~" . (100 - $percentUK) . "%\n";
}
