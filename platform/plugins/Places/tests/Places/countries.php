<?php

function Places_check_validation($language)
{
    //$url = "plugins/Places/js/lib/countries.js";
    $url = "/projects/qbix/Q/platform/plugins/Places/tests/Places/countries/en.js";
     //var cbc = Places.countriesByCode;
    $cbc = ["AF", "AX", "AL", "DZ", "AS", "AD", "AO", "AI", "AQ", "AG", "AR", "AM", "AW", "AU", "AT", "AZ", "BS",
    "BH", "BD", "BB", "BY", "BE", "BZ", "BJ", "BM", "BT", "BO", "BQ", "BA", "BW", "BV", "BR", "IO", "BN", "BG", "BF", "BI", "KH", "CM", "CA", "CV", "KY", "CF", "TD", "CL", "CN",
    "CX", "CC", "CO", "KM", "CG", "CD", "CK", "CR", "CI", "HR", "CU", "CW", "CY", "CZ", "DK", "DJ", "DM", "DO", "EC", "EG", "SV", "GQ", "ER", "EE", "ET", "FK", "FO", "FJ", "FI",
    "FR", "GF", "PF", "TF", "GA", "GM", "GE", "DE", "GH", "GI", "GR", "GL", "GD", "GP", "GU", "GT", "GG", "GN", "GW", "GY", "HT", "HM", "HN", "HK", "HU", "IS", "IN", "ID", "IR",
    "IQ", "IE", "IM", "IL", "IT", "JM", "JP", "JE", "JO", "KZ", "KE", "KI", "KP", "KR", "KW", "KG", "LA", "LV", "LB", "LS", "LR", "LY", "LI", "LT", "LU", "MO", "MK", "MG", "MW",
    "MY", "MV", "ML", "MT", "MH", "MQ", "MR", "MU", "YT", "MX", "FM", "MD", "MC", "MN", "ME", "MS", "MA", "MZ", "MM", "NA", "NR", "NP", "NL", "NC", "NZ", "NI", "NE", "NG", "NU",
    "NF", "MP", "NO", "OM", "PK", "PW", "PS", "PA", "PG", "PY", "PE", "PH", "PN", "PL", "PT", "PR", "QA", "RE", "RO", "RU", "RW", "BL", "SH", "KN", "LC", "MF", "PM", "VC", "WS",
    "SM", "ST", "SA", "SN", "RS", "SC", "SL", "SG", "SX", "SK", "SI", "SB", "SO", "ZA", "GS", "SS", "ES", "LK", "SD", "SR", "SJ", "SZ", "SE", "CH", "SY", "TW", "TJ", "TZ", "TH",
    "TL", "TG", "TK", "TO", "TT", "TN", "TR", "TM", "TC", "TV", "UG", "UA", "AE", "GB", "US", "UM", "UY", "UZ", "VU", "VA", "VE", "VN", "VG", "VI", "WF", "EH", "YE", "ZM", "ZW"];

    //$dir = '/projects/qbix/Q/platform/plugins/Places/web/js/lib/countries/';
    $dir = '/projects/qbix/Q/platform/plugins/Places/tests/Places/countries/';

    $countruFiles = array_diff(scandir($dir), array('..', '.'));
    print_r($countruFiles);

    $results = array();
    if ($language) {
        foreach ($countruFiles as $key => $val) {
            if ($val) {
                $urlFile = $dir . $val;
                var_dump($urlFile);
                try {
                    $convert = file_get_contents($urlFile); //read the file

                     if ( count($convert)) {
                         $result = json_decode($urlFile);
                         var_dump($result);

                        // var_dump(array_udiff($cbc, $convert));
                     }else{
                         print_r(' else!!! ');
                     }
                } catch (Exception $e) {
                    // error message
                }
            }
        }

    }
}

Places_check_validation('ua');


/*
define ('APP_DIR', realpath('..'));

class Places_Check
{

    static function Places_check_validation($language)
    {
        //var cl = 'plugins/Places/js/lib/countries.js';
        //if (language) {
        //    cl = 'plugins/Places/js/lib/countries/'+language+'.js';
        $url = "plugins/Places/js/lib/countries.js";
        $results = array();
        if ($language) {
            $url = "plugins/Places/js/lib/countries/$language.js"; //"https://pixabay.com/api/?key=$key&q=$keywords&$optionString";
            $json = @file_get_contents($url);
            //$data = Q::json_decode($json, true);

            if (!isset($result)) {
                try {
                    $result = Q::json_decode($json);
                } catch (Exception $e) {

                }
            }
        }

    }
}*/