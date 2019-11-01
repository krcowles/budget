<?php
/**
 * This script will simply setup the budget table with the correct rolling 3-month
 * period for display and operation of the budget tracker (budget.php). Note that 
 * the files used will always contain the current year as part of the file name.
 * Hence when the year rolls over, a new set of data will begin.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license
 */
require "getBudgetData.php";

// format the data for the table:
$dsign = '<span>$</span><span>';
$negdollar = '<span class="negative">$</span><span class="negative">';
// account names and current month data
$entries = [];
$lines = [];
$bbal = 0;
$bal1 = 0;
$bal2 = 0;
$bal3 = 0;
$setup = false;
$new_budget = '';
$record_count = 0;
if ($status === 'OK') {
    for ($j=0; $j<count($account_names); $j++) {
        $bbal += intval($budgets[$j]);
        $bal1 += floatval($prev0[$j]);
        $bal2 += floatval($prev1[$j]);
        $bal3 += floatval($current[$j]);
        $entries[0] = $account_names[$j];
        $entries[1] = $budgets[$j];
        $entries[2] = $prev0[$j];
        $entries[3] = $prev1[$j];
        $entries[4] = $current[$j];
        if (empty($day[$j])) { // no autopay data yet
            $entries[5] = '';
            $entries[6] = '';
        } else {
            $entries[5] = $autopay[$j];
            $entries[6] = $day[$j];
        }
        array_push($lines, $entries);
    } 
} else {
    $new_budget = '<script type="text/javascript">' .
        'var ans = confirm("There is no account data;\n Do you wish to start ' .
        'a new budget?"); if (ans) {setup = true;} else {setup=false;}</script>';
    $setup = true;
}
if (!$setup) {
    $bbal = $dsign . number_format($bbal, 0, '.', ',') . '</span>';
    $bal1 = $dsign . number_format($bal1, 2, '.', ',') . '</span>';
    $bal2 = $dsign . number_format($bal2, 2, '.', ',') . '</span>';
    $bal3 = $dsign . number_format($bal3, 2, '.', ',') . '</span>';
    // first entry is the column header
    $first = true;
    foreach ($lines as &$line) { // assumes budget data is never negative
        for ($m=1; $m<5; $m++) {
            $bud = number_format($line[$m], 0, '.', ',');
            $dat = number_format($line[$m], 2, '.', ',');
            if ($line[$m] < 0) {
                $line[$m] = $negdollar . $dat . '</span>';
            } else {
                if ($m === 1) {
                    $line[1] = $dsign . $bud . '</span>';
                } else {
                    $line[$m] = $dsign . $dat . '</span>';
                }
                
            }
        }
    }
    // credit card data
    $card1 = [];
    $card2 = [];
    $card3 = [];
    $card4 = [];
    $crbalances = [];
    $cardno = 0;
    $cards = fopen($credit_data, "r");
    if ($cards !== false) {
        $cdcards = 1;
        $card_names = [];
        $card_dat = fgetcsv($cards);
        $crcards = 0;
        for ($q=0; $q<count($card_dat); $q+=2) {
            if ($card_dat[$q+1] === 'Credit') {
                $card_names[$crcards] = $card_dat[$q];
                $crcards++;
            }
        }
        $crbal = 0;
        while (($charges = fgetcsv($cards)) !== false) {
            if (strpos($charges[0], "next") !== false) {
                // skip the line containing "next"
                $dat = number_format($crbal, 2, '.', ',');
                $crbal = $dsign . $dat . '</span>';
                $crbalances[$cardno] = $crbal;
                $charges = fgetcsv($cards);
                $crbal = 0;
                $cardno++;
            }
            $crbal += floatVal($charges[0]);
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
        // record the last one
        $dat = number_format($crbal, 2, '.', ',');
        $crbal = $dsign . $dat . '</span>';
        $crbalances[$cardno] = $crbal;
    } else {
        $cdcards = "0";
    }
}
