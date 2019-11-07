<?php
/**
 * This module extracts the autopay information from the query string and applies
 * the charges to the account as specified. Note that budget.php always calculates
 * the balance, so no adjustment is needed to that entry.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "getBudgetData.php";

// query string data
$budget_row = filter_input(INPUT_GET, 'row', FILTER_SANITIZE_NUMBER_INT);
$method = filter_input(INPUT_GET, 'use');
$amount = floatval(filter_input(INPUT_GET, 'amt'));
$acct_charged = filter_input(INPUT_GET, 'acct');
$payee = filter_input(INPUT_GET, 'payto');
 
// update $budget_data
$current[$budget_row] = floatval($current[$budget_row]) - $amount;
$paid[$budget_row] = 'Y';
$handle = fopen($budget_data, "w");
fputcsv($handle, $headers);
for ($q=0; $q<count($account_names); $q++) {
    $output_array = array($account_names[$q], $budgets[$q], $prev0[$q],
        $prev1[$q], $current[$q], $autopay[$q], $day[$q], $paid[$q]);
    fputcsv($handle, $output_array);
}
fclose($handle);

// apply any credit card charge
require "getCrData.php";
$credit_index = "None";
for ($c=0; $c<count($cards); $c++) {
    if ($cards[$c] == $method) {
        switch ($c) {
        case 0:
            $credit_index = 'card1';
            break;
        case 1:
            $credit_index = 'card2';
            break;
        case 2:
            $credit_index = 'card3';
            break;
        case 3:
            $credit_index = 'card4';
        }
    }
}
$applied = date('d-M-y');
if ($credit_index !== "None") {
    $newcharge = array($acct_charged, $applied, $payee, $amount);
    array_push($credit_charges[$credit_index], $newcharge);
}
// write out the new data
$handle = fopen($credit_data, "w");
$indices = array('card1', 'card2', 'card3', 'card4');
fputcsv($handle, $headers);
for ($i=0; $i<$card_cnt; $i++) {
    if (count($credit_charges[$indices[$i]]) > 0) {
        foreach ($credit_charges[$indices[$i]] as $line) {
            fputcsv($handle, $line);
        }
    }
    if ($i < $card_cnt-1) {
        fputcsv($handle, array("next"));
    }  
}
fclose($handle);
// redirect
header("Location: ../main/budget.php");
