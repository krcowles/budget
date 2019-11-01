<?php
/**
 * This script will simply setup the budget table with the correct rolling 4-month
 * period for display and operation of the budget tracker (budget.php). Note that 
 * the files used will always contain the current year as part of the file name.
 * Hence when the year rolls over, a new set of data will begin.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license
 */
date_default_timezone_set('America/Denver');
$date = date("m/d/Y");
$digits = explode("/", $date);
// DEFINITIONS:
$file_root = "bud" . $digits[2];
$budget_data = $file_root . "_data.csv";
$credit_data = $file_root . "_charges.csv";
$month_names = array('January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December');
$thismo = intval($digits[0]) -1; // array index is zero-based
switch ($thismo) {
case 1:
    $month_set = array(11, 12, 1);
    $get_past  = 3;
    break;
case 2:
    $month_set = array(12, 1, 2);
    $get_past  = 2;
    break;
case 3:
    $month_set = array(1, 2, 3);
    $get_past  = 1;
default:
    $month_set = array($thismo-2, $thismo-1, $thismo);
    $get_past = 0;
}
// column headers
for ($i=0; $i<3; $i++) {
    $month[$i] = $month_names[$month_set[$i]];
}
// account names and current month data
$entries = [];
$temps = [];
$accts = fopen($budget_data, "r+");
$temp_accts = false;
if ($accts !== false) {
    while (($accounts = fgetcsv($accts)) !== false) {
        if (strpos($accounts[0], "Temporary") !== false) {
            $temp_accts = true;
            // skip current line
            $accounts = fgetcsv($accts);
        }
        for ($n=0; $n<5; $n++) {
            if ($n > 0) {
                if ($n === 1) {
                    $accounts[1] = "$  " . $accounts[1]; // no "cents"
                } else {
                    $accounts[$n] = "$  " .
                        number_format(floatval($accounts[$n]), 2);
                }
            }
        }
        if ($temp_accts) {
            array_push($temps, $accounts);
            if (strpos($accounts[0], "Undistributed") !== false) {
                break;
            }
        } else {
            array_push($entries, $accounts);
        }
    }
} else {
    echo "ACCOUNT DATA NOT FOUND";
}
// credit card data
$card1 = [];
$card2 = [];
$card3 = [];
$card4 = [];
$cardno = 0;
$cards = fopen($credit_data, "r+");
if ($cards !== false) {
    $card_names = fgetcsv($cards);
    $no_of_cards = count($card_names);
    while (($charges = fgetcsv($cards)) !== false) {
        if (strpos($charges[0], "next") !== false) {
            // skip the line containing "next"
            $charges = fgetcsv($cards);
            $cardno++;
        }
        switch ($cardno) {
        case 0:
            array_push($card1, $charges);
            break;
        case 1:
            array_push($card2, $charges);
            break;
        case 2:
            array_push($card3, $charges);
            break;
        case 3:
            array_push($card4, $charges);
        default:
            echo "ERROR encountered registering charge card names" .
                "in credit_data.csv";
        }
    }
} else {
    echo "CREDIT CARD DATA NOT FOUND";
}
