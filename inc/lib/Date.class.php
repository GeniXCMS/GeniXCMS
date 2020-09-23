<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.3 build date 20150126
 *
 * @version 1.1.11
 *
 * @link https://github.com/semplon/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @copyright 2014-2020 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class Date
{
    public static $timezone;

    public function __construct()
    {
        $timezone = Options::v('timezone');
        self::$timezone = ($timezone == '') ? 'UTC' : $timezone;
        date_default_timezone_set(self::$timezone);
    }

    public static function format($date, $format = '')
    {
        $timezone = self::$timezone; //Options::v('timezone');
        $time = strtotime($date);
        (empty($format)) ? $format = 'j F Y H:i A T' : $format = $format;
        $date = new DateTime($date);
        $date->setTimezone(new DateTimeZone(self::$timezone));
        $newdate = $date->format($format);

        return $newdate;
    }

    public static function local($date, $format = '')
    {
        setlocale(LC_TIME, Options::v('country_id'));
        (empty($format)) ? $format = '%#d %B %Y %H:%M %p' : $format = $format;
        $date = new DateTime($date);
        $date->setTimezone(new DateTimeZone(self::$timezone));
        $newdate = $date->format('Y/m/j H:i:s');
        $newdate = strftime($format, strtotime($newdate));

        return $newdate.' '.$date->format('T');
    }

    public static function timeZone()
    {
        $timezones = DateTimeZone::listAbbreviations();
//        $timezones = DateTimeZone::listAbbreviations(DateTimeZone::ALL);

        $cities = array();
        foreach ($timezones as $key => $zones) {
            foreach ($zones as $id => $zone) {
                //print_r($zone);
                /*
                 * Only get timezones explicitely not part of "Others".
                 * @see http://www.php.net/manual/en/timezones.others.php
                 */
                if (preg_match('/^(America|Antartica|Arctic|Asia|Atlantic|Europe|Indian|Pacific)\//', $zone['timezone_id'])
                    && $zone['timezone_id']) {
                    $cities[$zone['timezone_id']][] = $key;
                }
            }
        }

        // For each city, have a comma separated list of all possible timezones for that city.
        foreach ($cities as $key => $value) {
            $cities[$key] = implode(', ', $value);
        }

        // Only keep one city (the first and also most important) for each set of possibilities.
        $cities = array_unique($cities);

        // Sort by area/city name.
        ksort($cities);

        return $cities;
    }

    public static function optTimeZone($val = '')
    {
        $tz = self::timeZone();
        $opt = '';
        foreach ($tz as $t => $z) {
            ($t == $val) ? $sel = 'SELECTED' : $sel = '';
            $opt .= "<option value=\"{$t}\" $sel>{$t}</option>";
        }

        return $opt;
    }

    public static function optCountry($val = '')
    {
        $countries = self::countryList();
        $opt = '';
        foreach ($countries as $key => $value) {
            ($key == $val) ? $sel = 'SELECTED' : $sel = '';
            $opt .= "<option value=\"{$key}\" title=\"".htmlspecialchars($value)."\" {$sel}>".htmlspecialchars($value).'</option>';
        }

        return $opt;
    }

    public static function countryList()
    {
        $countries = array('AF' => 'Afghanistan',
                        'AX' => 'Åland Islands',
                        'AL' => 'Albania',
                        'DZ' => 'Algeria',
                        'AS' => 'American Samoa',
                        'AD' => 'Andorra',
                        'AO' => 'Angola',
                        'AI' => 'Anguilla',
                        'AQ' => 'Antarctica',
                        'AG' => 'Antigua and Barbuda',
                        'AR' => 'Argentina',
                        'AM' => 'Armenia',
                        'AW' => 'Aruba',
                        'AU' => 'Australia',
                        'AT' => 'Austria',
                        'AZ' => 'Azerbaijan',
                        'BS' => 'Bahamas',
                        'BH' => 'Bahrain',
                        'BD' => 'Bangladesh',
                        'BB' => 'Barbados',
                        'BY' => 'Belarus',
                        'BE' => 'Belgium',
                        'BZ' => 'Belize',
                        'BJ' => 'Benin',
                        'BM' => 'Bermuda',
                        'BT' => 'Bhutan',
                        'BO' => 'Bolivia',
                        'BA' => 'Bosnia and Herzegovina',
                        'BW' => 'Botswana',
                        'BV' => 'Bouvet Island',
                        'BR' => 'Brazil',
                        'IO' => 'British Indian Ocean Territory',
                        'BN' => 'Brunei Darussalam',
                        'BG' => 'Bulgaria',
                        'BF' => 'Burkina Faso',
                        'BI' => 'Burundi',
                        'KH' => 'Cambodia',
                        'CM' => 'Cameroon',
                        'CA' => 'Canada',
                        'CV' => 'Cape Verde',
                        'KY' => 'Cayman Islands',
                        'CF' => 'Central African Republic',
                        'TD' => 'Chad',
                        'CL' => 'Chile',
                        'CN' => 'China',
                        'CX' => 'Christmas Island',
                        'CC' => 'Cocos (Keeling) Islands',
                        'CO' => 'Colombia',
                        'KM' => 'Comoros',
                        'CG' => 'Congo',
                        'CD' => 'Congo, The Democratic Republic of The',
                        'CK' => 'Cook Islands',
                        'CR' => 'Costa Rica',
                        'CI' => "Cote D'ivoire",
                        'HR' => 'Croatia',
                        'CU' => 'Cuba',
                        'CY' => 'Cyprus',
                        'CZ' => 'Czech Republic',
                        'DK' => 'Denmark',
                        'DJ' => 'Djibouti',
                        'DM' => 'Dominica',
                        'DO' => 'Dominican Republic',
                        'EC' => 'Ecuador',
                        'EG' => 'Egypt',
                        'SV' => 'El Salvador',
                        'GQ' => 'Equatorial Guinea',
                        'ER' => 'Eritrea',
                        'EE' => 'Estonia',
                        'ET' => 'Ethiopia',
                        'FK' => 'Falkland Islands (Malvinas)',
                        'FO' => 'Faroe Islands',
                        'FJ' => 'Fiji',
                        'FI' => 'Finland',
                        'FR' => 'France',
                        'GF' => 'French Guiana',
                        'PF' => 'French Polynesia',
                        'TF' => 'French Southern Territories',
                        'GA' => 'Gabon',
                        'GM' => 'Gambia',
                        'GE' => 'Georgia',
                        'DE' => 'Germany',
                        'GH' => 'Ghana',
                        'GI' => 'Gibraltar',
                        'GR' => 'Greece',
                        'GL' => 'Greenland',
                        'GD' => 'Grenada',
                        'GP' => 'Guadeloupe',
                        'GU' => 'Guam',
                        'GT' => 'Guatemala',
                        'GG' => 'Guernsey',
                        'GN' => 'Guinea',
                        'GW' => 'Guinea-bissau',
                        'GY' => 'Guyana',
                        'HT' => 'Haiti',
                        'HM' => 'Heard Island and Mcdonald Islands',
                        'VA' => 'Holy See (Vatican City State)',
                        'HN' => 'Honduras',
                        'HK' => 'Hong Kong',
                        'HU' => 'Hungary',
                        'IS' => 'Iceland',
                        'IN' => 'India',
                        'ID' => 'Indonesia',
                        'IR' => 'Iran, Islamic Republic of',
                        'IQ' => 'Iraq',
                        'IE' => 'Ireland',
                        'IM' => 'Isle of Man',
                        'IL' => 'Israel',
                        'IT' => 'Italy',
                        'JM' => 'Jamaica',
                        'JP' => 'Japan',
                        'JE' => 'Jersey',
                        'JO' => 'Jordan',
                        'KZ' => 'Kazakhstan',
                        'KE' => 'Kenya',
                        'KI' => 'Kiribati',
                        'KP' => "Korea, Democratic People's Republic of",
                        'KR' => 'Korea, Republic of',
                        'KW' => 'Kuwait',
                        'KG' => 'Kyrgyzstan',
                        'LA' => "Lao People's Democratic Republic",
                        'LV' => 'Latvia',
                        'LB' => 'Lebanon',
                        'LS' => 'Lesotho',
                        'LR' => 'Liberia',
                        'LY' => 'Libyan Arab Jamahiriya',
                        'LI' => 'Liechtenstein',
                        'LT' => 'Lithuania',
                        'LU' => 'Luxembourg',
                        'MO' => 'Macao',
                        'MK' => 'Macedonia, The Former Yugoslav Republic of',
                        'MG' => 'Madagascar',
                        'MW' => 'Malawi',
                        'MY' => 'Malaysia',
                        'MV' => 'Maldives',
                        'ML' => 'Mali',
                        'MT' => 'Malta',
                        'MH' => 'Marshall Islands',
                        'MQ' => 'Martinique',
                        'MR' => 'Mauritania',
                        'MU' => 'Mauritius',
                        'YT' => 'Mayotte',
                        'MX' => 'Mexico',
                        'FM' => 'Micronesia, Federated States of',
                        'MD' => 'Moldova, Republic of',
                        'MC' => 'Monaco',
                        'MN' => 'Mongolia',
                        'ME' => 'Montenegro',
                        'MS' => 'Montserrat',
                        'MA' => 'Morocco',
                        'MZ' => 'Mozambique',
                        'MM' => 'Myanmar',
                        'NA' => 'Namibia',
                        'NR' => 'Nauru',
                        'NP' => 'Nepal',
                        'NL' => 'Netherlands',
                        'AN' => 'Netherlands Antilles',
                        'NC' => 'New Caledonia',
                        'NZ' => 'New Zealand',
                        'NI' => 'Nicaragua',
                        'NE' => 'Niger',
                        'NG' => 'Nigeria',
                        'NU' => 'Niue',
                        'NF' => 'Norfolk Island',
                        'MP' => 'Northern Mariana Islands',
                        'NO' => 'Norway',
                        'OM' => 'Oman',
                        'PK' => 'Pakistan',
                        'PW' => 'Palau',
                        'PS' => 'Palestinian Territory, Occupied',
                        'PA' => 'Panama',
                        'PG' => 'Papua New Guinea',
                        'PY' => 'Paraguay',
                        'PE' => 'Peru',
                        'PH' => 'Philippines',
                        'PN' => 'Pitcairn',
                        'PL' => 'Poland',
                        'PT' => 'Portugal',
                        'PR' => 'Puerto Rico',
                        'QA' => 'Qatar',
                        'RE' => 'Reunion',
                        'RO' => 'Romania',
                        'RU' => 'Russian Federation',
                        'RW' => 'Rwanda',
                        'SH' => 'Saint Helena',
                        'KN' => 'Saint Kitts and Nevis',
                        'LC' => 'Saint Lucia',
                        'PM' => 'Saint Pierre and Miquelon',
                        'VC' => 'Saint Vincent and The Grenadines',
                        'WS' => 'Samoa',
                        'SM' => 'San Marino',
                        'ST' => 'Sao Tome and Principe',
                        'SA' => 'Saudi Arabia',
                        'SN' => 'Senegal',
                        'RS' => 'Serbia',
                        'SC' => 'Seychelles',
                        'SL' => 'Sierra Leone',
                        'SG' => 'Singapore',
                        'SK' => 'Slovakia',
                        'SI' => 'Slovenia',
                        'SB' => 'Solomon Islands',
                        'SO' => 'Somalia',
                        'ZA' => 'South Africa',
                        'GS' => 'South Georgia and The South Sandwich Islands',
                        'ES' => 'Spain',
                        'LK' => 'Sri Lanka',
                        'SD' => 'Sudan',
                        'SR' => 'Suriname',
                        'SJ' => 'Svalbard and Jan Mayen',
                        'SZ' => 'Swaziland',
                        'SE' => 'Sweden',
                        'CH' => 'Switzerland',
                        'SY' => 'Syrian Arab Republic',
                        'TW' => 'Taiwan, Province of China',
                        'TJ' => 'Tajikistan',
                        'TZ' => 'Tanzania, United Republic of',
                        'TH' => 'Thailand',
                        'TL' => 'Timor-leste',
                        'TG' => 'Togo',
                        'TK' => 'Tokelau',
                        'TO' => 'Tonga',
                        'TT' => 'Trinidad and Tobago',
                        'TN' => 'Tunisia',
                        'TR' => 'Turkey',
                        'TM' => 'Turkmenistan',
                        'TC' => 'Turks and Caicos Islands',
                        'TV' => 'Tuvalu',
                        'UG' => 'Uganda',
                        'UA' => 'Ukraine',
                        'AE' => 'United Arab Emirates',
                        'GB' => 'United Kingdom',
                        'US' => 'United States',
                        'UM' => 'United States Minor Outlying Islands',
                        'UY' => 'Uruguay',
                        'UZ' => 'Uzbekistan',
                        'VU' => 'Vanuatu',
                        'VE' => 'Venezuela',
                        'VN' => 'Viet Nam',
                        'VG' => 'Virgin Islands, British',
                        'VI' => 'Virgin Islands, U.S.',
                        'WF' => 'Wallis and Futuna',
                        'EH' => 'Western Sahara',
                        'YE' => 'Yemen',
                        'ZM' => 'Zambia',
                        'ZW' => 'Zimbabwe', );

        return $countries;
    }
}
