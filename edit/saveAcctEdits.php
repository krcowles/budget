<?php
/**
 * This module will make changes to the $budget_data file based on user input:
 * expense items, name change, etc.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../utilities/getBudgetData.php";

$newname   = isset($_GET['newname']) ? filter_input(INPUT_GET, 'newname') : false;
$acct_name = isset($_GET['acct_name']) ?
    filter_input(INPUT_GET, 'acct_name') : false;
$edit_row  = isset($_GET['edit_row']) ? 
    filter_input(INPUT_GET, 'edit_row', FILTER_VALIDATE_INT) : false;
$chg_type  = isset($_GET['chg_type']) ? filter_input(INPUT_GET, 'chg_type') : false;
$amt       = isset($_GET['amt'])  ? floatval(filter_input(INPUT_GET, 'amt')) : false;
$payee     = isset($_GET['payto']) ? filter_input(INPUT_GET, 'payto') : false;

if ($amt && $chg_type) {
    date_default_timezone_set('America/Denver');
    $date = date("d-M-y");
    $current[$edit_row] = floatval($current[$edit_row]) - $amt;
    $exptype = substr($chg_type, 0, 3);
    if ($exptype === "deb" || $exptype === 'non') {
        // record debit expense
    } elseif ($exptype === "car") {
        // record credit expense
        include "../utilities/getCrData.php";
        $entry = array($acct_name, $date, $payee, $amt);
        array_push($credit_charges[$chg_type], $entry);
        $handle = fopen($credit_data, "w");
        fputcsv($handle, $crHeaders);
        foreach ($credit_charges as $cardset) {
            foreach ($cardset as $line) {
                fputcsv($handle, $line);
            }
        }
        fclose($handle);
    }
}

// write budget data back out
$handle = fopen($budget_data, "w");
fputcsv($handle, $headers);
for ($i=0; $i<count($account_names); $i++) {
    $line = array($account_names[$i], $budgets[$i], $prev0[$i], $prev1[$i],
        $current[$i], $autopay[$i], $day[$i], $paid[$i], $income[$i]);
    fputcsv($handle, $line);
}
fclose($handle);
echo "OK";
