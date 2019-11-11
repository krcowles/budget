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
$datadir = isset($_GET['dir']) ? filter_input(INPUT_GET, 'dir') : false;
if ($datadir) {
    $user_dir = "../main/current_dir.txt";
    file_put_contents($user_dir,  $datadir);
}
require "../utilities/getBudgetData.php"; // produces $status
require "../utilities/getCrData.php";     // echos issue if no charges

// format the dollar amounts for the table:
$dsign = '<span>$</span><span>';
$negdollar = '<span class="negative">$</span><span class="negative">';

// account names and current month data
$entries = []; // the array representing the .csv data
$lines = []; // a line of data in the budget
$bbal = 0; // sum of budgeted amounts
$bal1 = 0; // sum of $prev0
$bal2 = 0; // sum of $prev1
$bal3 = 0; // sum of $current
$setup = false; // true indicates new data needs to be entered before proceeding
$new_budget = '';
$record_count = 0;

// proceed with values for main budget items:
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
            $entries[7] = '';
        } else {
            $entries[5] = $autopay[$j];
            $entries[6] = $day[$j];
            $entries[7] = $paid[$j];
        }
        $entries[8] = $income[$j];
        array_push($lines, $entries);
    }  
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
} // end of $status = OK

// get Credit Card info: requires formatting for budget.php
if ($crStatus === "OK") {
    $card_indx = array('card1', 'card2', 'card3', 'card4');
    $crbalances = [];
    $crtotal = 0; 
    for ($k=0; $k<$card_cnt; $k++) {
        $crbalances[$k] = 0;
        $indx = $card_indx[$k];
        $no_of_charges = count($credit_charges[$indx]);
        for ($j=0; $j<$no_of_charges; $j++) {
            $credit_entry = $credit_charges[$indx][$j];
            $crbalances[$k] = $crbalances[$k] += floatVal($credit_entry[3]);
        }
        $crtotal += $crbalances[$k];
    }
    // add formatting
    for ($n=0; $n<$card_cnt; $n++) {
        $dat = number_format($crbalances[$n], 2, '.', ',');
        $crbalances[$n] = $dsign . $dat . '</span>';
    }
    // if a rollover, update the oldsums.txt file (in user dir)
    if ($rollover || $rollyear) {
        $olddat = file($oldsumstxt, FILE_IGNORE_NEW_LINES);
        for ($t=0; $t<$card_cnt; $t++) {
            $line = explode(",", $olddat[$t]); // $Line[0] is card name
            $line[1] = $line[2];
            $line[2] = $crbalances[$t];
            $olddat[$t] = implode(",", $line) . PHP_EOL;
        }
        file_put_contents($oldsumstxt, $olddat);
    }
    // update monthly balances by adding in credit charges
    $oldsums = file($oldsumstxt, FILE_IGNORE_NEW_LINES);
    // there should be one entry per charge card:
    if (count($oldsums) !== $card_cnt) {
        echo "Mismatched current card data vs old card data";
    }
    $old_card_balances = [];
    $old_card_sum0 = 0;
    $old_card_sum1 = 0;
    for ($r=0; $r<$card_cnt; $r++) {
        $old_data = explode(",", $oldsums[$r]);
        $vala = floatval($old_data[1]);
        $valb = floatval($old_data[2]);
        $old_card_sum0 += $vala;
        $old_card_sum1 += $valb;
        $val1 = $dsign . number_format($vala, 2, '.', ',');
        $val2 = $dsign . number_format($valb, 2, '.', ',');
        $old_card_balances[$r] = array($val1, $val2);
    }
    $bal1 += $old_card_sum0;
    $bal2 += $old_card_sum1;
    $bal3 += $crtotal;
    // format remaining data
    $obal = number_format($bal3, 2, '.', ',');
    $bbal = $dsign . number_format($bbal, 0, '.', ',') . '</span>';
    $bal1 = $dsign . number_format($bal1, 2, '.', ',') . '</span>';
    $bal2 = $dsign . number_format($bal2, 2, '.', ',') . '</span>';
    $bal3 = $dsign . number_format($bal3, 2, '.', ',') . '</span>';
} 
if ($status !== "OK" || $crStatus !== "OK") {
    // Take appropriate action based on message
    if ($status !== "OK") {  // treat these first, as more serious
        $ecode = intval(substr($status, 1, 2));
        switch ($ecode) {
        case 1: // previous year budget data file not present
            break;
        case 2: //budget data file not present
            echo '<script type="text/javascript">alert("No budget has been ' .
                'created;\nYou will be redirected to the budget-creation page");' .
                'window.open("../edit/newBudget.html", "_self");</script>';
            break;
        case 3: // couldn't open $budget_data
            echo '<script type="text/javascript">alert("Budget data file couln not' .
                ' be opened:\nContact administrator");';
            break;
        case 4: // no error msg at this time
            break;
        case 5: // previous year budget data file could not be opened
            break;
        }
    } else {
        $ecode = intval(substr($crSetatus, 1, 2));
        switch ($ecode) {
        case 6: // credit data has yet to be entered (file exists)
            break;
        case 7: // credit data file could not be opened
            break;
        }
    }
}