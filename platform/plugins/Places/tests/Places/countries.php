<?php

function Places_check_validation($countries, $cbc1)
{
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

    if ($countries) {
        $json_result = json_decode($countries, true);
        if (is_null($json_result)) {
            echo "Something dun gone blowed up!\n JSON Error:";
            switch (json_last_error()) {
                case JSON_ERROR_NONE:
                    echo ' - No error has occurred';
                    break;
                case JSON_ERROR_DEPTH:
                    echo ' - The maximum stack depth has been exceeded';
                    break;
                case JSON_ERROR_STATE_MISMATCH:
                    echo ' - Invalid or malformed JSON';
                    break;
                case JSON_ERROR_CTRL_CHAR:
                    echo ' - Control character error, possibly incorrectly encoded';
                    break;
                case JSON_ERROR_SYNTAX:
                    echo ' - Syntax error';
                    break;
                case JSON_ERROR_UTF8:
                    echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
                    break;
                default:
                    echo ' - Unknown error';
                    break;
            }

            echo PHP_EOL;
        } else {
            $error = [];
            foreach ($json_result as $key => $val) {
                if ($cbc[$key] == $val[1])
                    echo '';  //echo "cbc= $cbc[$key]  val = $val[1]\n\n";
                else {
                    $error[] = "For '$val[0]' code = '$val[1]' but standard = '$cbc[$key]' \n\n";

                }
            }
            if (!empty($error)) {
                print_r($error);
            } else {
                echo "No errors are detected\n";
            }
        }

    }
}

function Places_find_file()
{
    //$dir = '/projects/qbix/Q/platform/plugins/Places/tests/Places/countries/';
    $dir = '/projects/qbix/Q/platform/plugins/Places/web/js/lib/countries/';
    $countryFiles = array_diff(scandir($dir), array('..', '.')); //get all files from countries folder

    foreach ($countryFiles as $key => $val) {
        preg_match("/^.*\.js$/", $val, $output_array); // use only .js file

        if ($output_array) {
            $urlFile = $dir . $val;
            try {
                $string = file_get_contents($urlFile);
                //gets counries and counrie's code
                preg_match_all("/(\[(\s+\[.*\],?)+\s+\])|({(\s+\\\".*\\\",?)+\s+})/", $string, $matches);

                if (is_null($matches)) {
                    echo "\nFile : $val\n";
                    echo "Something dun gone blowed up!";
                } else {
                    echo "\n********** File : $val **********\n";

                    //gets and sorts counrie's code
                    $cCode = json_decode($matches[0][1], true);
                    $cCode = array_keys($cCode);
                    array_multisort($cCode, SORT_ASC);

                    Places_check_validation($matches[0][0], $cCode);
                }
            } catch (Exception $e) {
                echo "\n error message = $e\n";
            }
        }
    }
}

Places_find_file();


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
            $url = "plugins/Places/js/lib/countries/$language.js";
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