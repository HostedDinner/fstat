<?php
/**
 * Representing a country object
 * parameters for construct are the filenames of the csv file(s)
 *
 * @author Fabian
 */
class Country {
    private $filenames;
    
    private $country = "Unknown";
    private $countryShort= "fam";
    
    
    public function __construct() {
        $this->filenames = func_get_args();
    }
    
    
    public function parse($lookupip){
        //$lookupip_long = sprintf("%u", ip2long($lookupip));
        $lookupip_bin = inet_pton("$lookupip");
        
        foreach ($this->filenames as $filename){
            $isFound = $this->find($filename, $lookupip_bin);
            if($isFound){
                //found IP, must not search in further files
                break;
            }
        }
    }
    
    
    private function find($filename, $lookupip_bin){
        if(is_readable($filename)){
            $handle = fopen($filename,"r");
            
            //Seek to the end
            fseek($handle, 0, SEEK_END);
            $high = ftell($handle);
            $low = 0;

            while ($low <= $high) {
                $mid = floor(($low + $high) / 2);

                fseek($handle, $mid);

                if ($mid != 0) {
                    //Read a line to move to eol
                    $line = fgets($handle);
                }

                //Read a line to get data
                $data = fgetcsv($handle, 200, ",");
                
                //End of File Reached
                if($data === false){
                    break;
                }
                
                $test = self::ipInBinRange(inet_pton($data[0]), inet_pton($data[1]), $lookupip_bin);
                
                
                if ($test == 0) {
                    $this->countryShort = strtolower($data[2]);
                    //$this->country = ucwords(strtolower($data[4]));
                    $this->country = self::getCountryName(strtolower($data[2]));
                    fclose($handle);
                    //break here, we have found our entry
                    return true; 
                } elseif($test < 0) {
                    $high = $mid - 1;
                } else {
                    $low = $mid + 1;
                }
            }

            fclose($handle);
            return false;
        }else{
            return false;
        }
    }
    
    //checks if a ip is in given Range (binary)
    //-1 ip is smaller
    //0 in Range
    //1 IP is greater
    //NULL if range parameters are not the same protoversion
    //IPv4 < IPv6
    private static function ipInBinRange($start, $end, $ip){
        $length_range = strlen($start);

        //start and end are same proto version
        if($length_range != strlen($end)){
            return FALSE;
        }

        $length_ip = strlen($ip);

        if($length_ip < $length_range){
            return -1; //range is Ipv6, ip is IPv4
        }elseif($length_ip > $length_range){
            return 1; //range is Ipv4, ip is IPv6
        }else{
            if(strcmp($ip, $start) < 0){
                return -1;
            }elseif(strcmp($ip, $end) <= 0){
                return 0;
            }else{
                return 1;
            }
        }
    }
    
    
    //Basic Getter
    public function getCountry(){
        return $this->country;
    }
    
    public function getCountryShort(){
        return $this->countryShort;
    }
    
    //Extended Getter
    public static function getCountryOffset($country){
        if(isset(self::$countryOffset[$country])){
            return self::$countryOffset[$country];
        }else{
            //Ommit last 4 Chars: ".png"
            $shorterCountry = substr($country, 0, -4);
            if(isset(self::$countryOffset[$shorterCountry])){
                return self::$countryOffset[$shorterCountry];
            }else{
                return 0;
            }
        }
    }

    
    public static function getCountryName($abbr){
        if(isset(self::$countryNames[$abbr])){
            return self::$countryNames[$abbr];
        }else{
            return "Unknown";
        }
    }

    
    private static $countryOffset = array(
        'ad' => -0,
        'ae' => -11,
        'af' => -22,
        'ag' => -33,
        'ai' => -44,
        'al' => -55,
        'am' => -66,
        'an' => -77,
        'ao' => -88,
        'ar' => -99,
        'as' => -110,
        'at' => -121,
        'au' => -132,
        'aw' => -143,
        'ax' => -154,
        'az' => -165,
        'ba' => -176,
        'bb' => -187,
        'bd' => -198,
        'be' => -209,
        'bf' => -220,
        'bg' => -231,
        'bh' => -242,
        'bi' => -253,
        'bj' => -264,
        'bm' => -275,
        'bn' => -286,
        'bo' => -297,
        'br' => -308,
        'bs' => -319,
        'bt' => -330,
        'bv' => -341,
        'bw' => -352,
        'by' => -363,
        'bz' => -374,
        'ca' => -385,
        'catalonia' => -396,
        'cc' => -407,
        'cd' => -418,
        'cf' => -429,
        'cg' => -440,
        'ch' => -451,
        'ci' => -462,
        'ck' => -473,
        'cl' => -484,
        'cm' => -495,
        'cn' => -506,
        'co' => -517,
        'cr' => -528,
        'cs' => -539,
        'cu' => -550,
        'cv' => -561,
        'cx' => -572,
        'cy' => -583,
        'cz' => -594,
        'de' => -605,
        'dj' => -616,
        'dk' => -627,
        'dm' => -638,
        'do' => -649,
        'dz' => -660,
        'ec' => -671,
        'ee' => -682,
        'eg' => -693,
        'eh' => -704,
        'england' => -715,
        'er' => -726,
        'es' => -737,
        'et' => -748,
        'europeanunion' => -759,
        'fam' => -770,
        'fi' => -781,
        'fj' => -792,
        'fk' => -803,
        'fm' => -814,
        'fo' => -825,
        'fr' => -836,
        'ga' => -847,
        'gb' => -858,
        'gd' => -869,
        'ge' => -880,
        'gf' => -891,
        'gh' => -902,
        'gi' => -913,
        'gl' => -924,
        'gm' => -935,
        'gn' => -946,
        'gp' => -957,
        'gq' => -968,
        'gr' => -979,
        'gs' => -990,
        'gt' => -1001,
        'gu' => -1012,
        'gw' => -1023,
        'gy' => -1034,
        'hk' => -1045,
        'hm' => -1056,
        'hn' => -1067,
        'hr' => -1078,
        'ht' => -1089,
        'hu' => -1100,
        'id' => -1111,
        'ie' => -1122,
        'il' => -1133,
        'in' => -1144,
        'io' => -1155,
        'iq' => -1166,
        'ir' => -1177,
        'is' => -1188,
        'it' => -1199,
        'jm' => -1210,
        'jo' => -1221,
        'jp' => -1232,
        'ke' => -1243,
        'kg' => -1254,
        'kh' => -1265,
        'ki' => -1276,
        'km' => -1287,
        'kn' => -1298,
        'kp' => -1309,
        'kr' => -1320,
        'kw' => -1331,
        'ky' => -1342,
        'kz' => -1353,
        'la' => -1364,
        'lb' => -1375,
        'lc' => -1386,
        'li' => -1397,
        'lk' => -1408,
        'lr' => -1419,
        'ls' => -1430,
        'lt' => -1441,
        'lu' => -1452,
        'lv' => -1463,
        'ly' => -1474,
        'ma' => -1485,
        'mc' => -1496,
        'md' => -1507,
        'me' => -1518,
        'mg' => -1529,
        'mh' => -1540,
        'mk' => -1551,
        'ml' => -1562,
        'mm' => -1573,
        'mn' => -1584,
        'mo' => -1595,
        'mp' => -1606,
        'mq' => -1617,
        'mr' => -1628,
        'ms' => -1639,
        'mt' => -1650,
        'mu' => -1661,
        'mv' => -1672,
        'mw' => -1683,
        'mx' => -1694,
        'my' => -1705,
        'mz' => -1716,
        'na' => -1727,
        'nc' => -1738,
        'ne' => -1749,
        'nf' => -1760,
        'ng' => -1771,
        'ni' => -1782,
        'nl' => -1793,
        'no' => -1804,
        'np' => -1815,
        'nr' => -1826,
        'nu' => -1837,
        'nz' => -1848,
        'om' => -1859,
        'pa' => -1870,
        'pe' => -1881,
        'pf' => -1892,
        'pg' => -1903,
        'ph' => -1914,
        'pk' => -1925,
        'pl' => -1936,
        'pm' => -1947,
        'pn' => -1958,
        'pr' => -1969,
        'ps' => -1980,
        'pt' => -1991,
        'pw' => -2002,
        'py' => -2013,
        'qa' => -2024,
        're' => -2035,
        'ro' => -2046,
        'rs' => -2057,
        'ru' => -2068,
        'rw' => -2079,
        'sa' => -2090,
        'sb' => -2101,
        'sc' => -2112,
        'scotland' => -2123,
        'sd' => -2134,
        'se' => -2145,
        'sg' => -2156,
        'sh' => -2167,
        'si' => -2178,
        'sj' => -2189,
        'sk' => -2200,
        'sl' => -2211,
        'sm' => -2222,
        'sn' => -2233,
        'so' => -2244,
        'sr' => -2255,
        'st' => -2266,
        'sv' => -2277,
        'sy' => -2288,
        'sz' => -2299,
        'tc' => -2310,
        'td' => -2321,
        'tf' => -2332,
        'tg' => -2343,
        'th' => -2354,
        'tj' => -2365,
        'tk' => -2376,
        'tl' => -2387,
        'tm' => -2398,
        'tn' => -2409,
        'to' => -2420,
        'tr' => -2431,
        'tt' => -2442,
        'tv' => -2453,
        'tw' => -2464,
        'tz' => -2475,
        'ua' => -2486,
        'ug' => -2497,
        'um' => -2508,
        'us' => -2519,
        'uy' => -2530,
        'uz' => -2541,
        'va' => -2552,
        'vc' => -2563,
        've' => -2574,
        'vg' => -2585,
        'vi' => -2596,
        'vn' => -2607,
        'vu' => -2618,
        'wales' => -2629,
        'wf' => -2640,
        'ws' => -2651,
        'ye' => -2662,
        'yt' => -2673,
        'za' => -2684,
        'zm' => -2695,
        'zw' => -2706
    );
    
    private static $countryNames = array(
        'ad' => 'Andorra',
        'ae' => 'United Arab Emirates',
        'af' => 'Afghanistan',
        'ag' => 'Antigua And Barbuda',
        'ai' => 'Anguilla',
        'al' => 'Albania',
        'am' => 'Armenia',
        'an' => 'Netherlands Antilles',
        'ao' => 'Angola',
        'aq' => 'Antarctica',
        'ar' => 'Argentina',
        'as' => 'American Samoa',
        'at' => 'Austria',
        'au' => 'Australia',
        'aw' => 'Aruba',
        'ax' => 'Finland',
        'az' => 'Azerbaijan',
        'ba' => 'Bosnia and Herzegovina',
        'bb' => 'Barbados',
        'bd' => 'Bangladesh',
        'be' => 'Belgium',
        'bf' => 'Burkina Faso',
        'bg' => 'Bulgaria',
        'bh' => 'Bahrain',
        'bi' => 'Burundi',
        'bj' => 'Benin',
        'bm' => 'Bermuda',
        'bn' => 'Brunei Darussalam',
        'bo' => 'Bolivia',
        'br' => 'Brazil',
        'bs' => 'Bahamas',
        'bt' => 'Bhutan',
        'bv' => 'Bouvet Island',
        'bw' => 'Botswana',
        'by' => 'Belarus',
        'bz' => 'Belize',
        'ca' => 'Canada',
        'cd' => 'The Democratic Republic of the Congo',
        'cf' => 'Central African Republic',
        'cg' => 'Congo',
        'ch' => 'Switzerland',
        'ci' => 'Cote D\'ivoire',
        'ck' => 'Cook Islands',
        'cl' => 'Chile',
        'cm' => 'Cameroon',
        'cn' => 'China',
        'co' => 'Colombia',
        'cr' => 'Costa Rica',
        'cs' => 'Serbia and Montenegro',
        'cu' => 'Cuba',
        'cv' => 'Cape Verde',
        'cy' => 'Cyprus',
        'cz' => 'Czech Republic',
        'de' => 'Germany',
        'dj' => 'Djibouti',
        'dk' => 'Denmark',
        'dm' => 'Dominica',
        'do' => 'Dominican Republic',
        'dz' => 'Algeria',
        'ec' => 'Ecuador',
        'ee' => 'Estonia',
        'eg' => 'Egypt',
        'er' => 'Eritrea',
        'es' => 'Spain',
        'et' => 'Ethiopia',
        'fi' => 'Finland',
        'fj' => 'Fiji',
        'fk' => 'Falkland Islands (malvinas)',
        'fm' => 'Federated States of Micronesia',
        'fo' => 'Faroe Islands',
        'fr' => 'France',
        'ga' => 'Gabon',
        'gb' => 'United Kingdom',
        'gd' => 'Grenada',
        'ge' => 'Georgia',
        'gf' => 'French Guiana',
        'gg' => 'Guernsey',
        'gh' => 'Ghana',
        'gi' => 'Gibraltar',
        'gl' => 'Greenland',
        'gm' => 'Gambia',
        'gn' => 'Guinea',
        'gp' => 'Guadeloupe',
        'gq' => 'Equatorial Guinea',
        'gr' => 'Greece',
        'gs' => 'South Georgia and the South Sandwich Islands',
        'gt' => 'Guatemala',
        'gu' => 'Guam',
        'gw' => 'Guinea-bissau',
        'gy' => 'Guyana',
        'hk' => 'Hong Kong',
        'hn' => 'Honduras',
        'hr' => 'Croatia',
        'ht' => 'Haiti',
        'hu' => 'Hungary',
        'id' => 'Indonesia',
        'ie' => 'Ireland',
        'il' => 'Israel',
        'im' => 'Isle Of Man',
        'in' => 'India',
        'io' => 'British Indian Ocean Territory',
        'iq' => 'Iraq',
        'ir' => 'Islamic Republic of Iran',
        'is' => 'Iceland',
        'it' => 'Italy',
        'je' => 'Jersey',
        'jm' => 'Jamaica',
        'jo' => 'Jordan',
        'jp' => 'Japan',
        'ke' => 'Kenya',
        'kg' => 'Kyrgyzstan',
        'kh' => 'Cambodia',
        'ki' => 'Kiribati',
        'km' => 'Comoros',
        'kn' => 'Saint Kitts and Nevis',
        'kp' => 'Democratic People\'s Republic of Korea',
        'kr' => 'Republic of Korea',
        'kw' => 'Kuwait',
        'ky' => 'Cayman Islands',
        'kz' => 'Kazakhstan',
        'la' => 'Lao People\'s Democratic Republic',
        'lb' => 'Lebanon',
        'lc' => 'Saint Lucia',
        'li' => 'Liechtenstein',
        'lk' => 'Sri Lanka',
        'lr' => 'Liberia',
        'ls' => 'Lesotho',
        'lt' => 'Lithuania',
        'lu' => 'Luxembourg',
        'lv' => 'Latvia',
        'ly' => 'Libyan Arab Jamahiriya',
        'ma' => 'Morocco',
        'mc' => 'Monaco',
        'md' => 'Republic of Moldova',
        'me' => 'Montenegro',
        'mf' => 'Saint Martin',
        'mg' => 'Madagascar',
        'mh' => 'Marshall Islands',
        'mk' => 'The Former Yugoslav Republic of Macedonia',
        'ml' => 'Mali',
        'mm' => 'Myanmar',
        'mn' => 'Mongolia',
        'mo' => 'Macao',
        'mp' => 'Northern Mariana Islands',
        'mq' => 'Martinique',
        'mr' => 'Mauritania',
        'ms' => 'Montserrat',
        'mt' => 'Malta',
        'mu' => 'Mauritius',
        'mv' => 'Maldives',
        'mw' => 'Malawi',
        'mx' => 'Mexico',
        'my' => 'Malaysia',
        'mz' => 'Mozambique',
        'na' => 'Namibia',
        'nc' => 'New Caledonia',
        'ne' => 'Niger',
        'nf' => 'Norfolk Island',
        'ng' => 'Nigeria',
        'ni' => 'Nicaragua',
        'nl' => 'Netherlands',
        'no' => 'Norway',
        'np' => 'Nepal',
        'nr' => 'Nauru',
        'nu' => 'Niue',
        'nz' => 'New Zealand',
        'om' => 'Oman',
        'pa' => 'Panama',
        'pe' => 'Peru',
        'pf' => 'French Polynesia',
        'pg' => 'Papua New Guinea',
        'ph' => 'Philippines',
        'pk' => 'Pakistan',
        'pl' => 'Poland',
        'pm' => 'Saint Pierre And Miquelon',
        'pr' => 'Puerto Rico',
        'ps' => 'Palestinian Territory, Occupied',
        'pt' => 'Portugal',
        'pw' => 'Palau',
        'py' => 'Paraguay',
        'qa' => 'Qatar',
        're' => 'Reunion',
        'ro' => 'Romania',
        'rs' => 'Serbia',
        'ru' => 'Russian Federation',
        'rw' => 'Rwanda',
        'sa' => 'Saudi Arabia',
        'sb' => 'Solomon Islands',
        'sc' => 'Seychelles',
        'sd' => 'Sudan',
        'se' => 'Sweden',
        'sg' => 'Singapore',
        'si' => 'Slovenia',
        'sk' => 'Slovakia',
        'sl' => 'Sierra Leone',
        'sm' => 'San Marino',
        'sn' => 'Senegal',
        'so' => 'Somalia',
        'sr' => 'Suriname',
        'st' => 'Sao Tome and Principe',
        'sv' => 'El Salvador',
        'sy' => 'Syrian Arab Republic',
        'sz' => 'Swaziland',
        'tc' => 'Turks and Caicos Islands',
        'td' => 'Chad',
        'tf' => 'French Southern Territories',
        'tg' => 'Togo',
        'th' => 'Thailand',
        'tj' => 'Tajikistan',
        'tk' => 'Tokelau',
        'tl' => 'Timor-leste',
        'tm' => 'Turkmenistan',
        'tn' => 'Tunisia',
        'to' => 'Tonga',
        'tr' => 'Turkey',
        'tt' => 'Trinidad And Tobago',
        'tv' => 'Tuvalu',
        'tw' => 'Taiwan',
        'tz' => 'United Republic of Tanzania',
        'ua' => 'Ukraine',
        'ug' => 'Uganda',
        'um' => 'United States Minor Outlying Islands',
        'us' => 'United States',
        'uy' => 'Uruguay',
        'uz' => 'Uzbekistan',
        'va' => 'Holy See (Vatican City State)',
        'vc' => 'Saint Vincent and the Grenadines',
        've' => 'Venezuela',
        'vg' => 'Virgin Islands, British',
        'vi' => 'Virgin Islands, U.S.',
        'vn' => 'Viet Nam',
        'vu' => 'Vanuatu',
        'wf' => 'Wallis and Futuna',
        'ws' => 'Samoa',
        'ye' => 'Yemen',
        'yt' => 'Mayotte',
        'za' => 'South Africa',
        'zm' => 'Zambia',
        'zw' => 'Zimbabwe'
    );

}
?>