<?php
/**
 * This module will scan the budget from top to bottom and distribute the entered
 * income into accounts where income has yet to be received (empty or partial).
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license
 */
require "getBudgetData.php";
require_once "budgetFunctions.php";

$funds = floatval(filter_input(INPUT_GET, 'funds', FILTER_SANITIZE_NUMBER_FLOAT));

$budgets = cleanupExcel($budgets);
$income  = cleanupExcel($income);
for ($i=0; $i<count($account_names); $i++) {
    $paidin = floatval($income[$i]);
    $budamt = floatval($budgets[$i]);
    $currbal = floatval($current[$i]);
    if ($paidin >= 0) {
        if ($paidin < $budamt) {
            $delta = $budamt - $paidin;
            if ($funds <= $delta) {
                $income[$i] = floatval($income[$i]) + $funds;
                $current[$i] = $currbal + $funds; 
                $funds = 0;
                break;
            } else {
                $funds -= $delta;
                $income[$i] = $budamt;
                $current[$i] = $currbal + $delta;
            }
        }
    }
}
if ($funds > 0) {
    for ($k=0; $k<count($account_names); $k++) {
        if ($account_names[$k] == 'Undistributed Funds') {
            $current[$k] = floatval($current[$k]) + $funds;
            break; 
        }
    }
}
// write the new budget
$handle = fopen($budget_data, "w");
fputcsv($handle, $headers);
for ($j=0; $j<count($account_names); $j++) {
    $line = array($account_names[$j], $budgets[$j], $prev0[$j], $prev1[$j],
        $current[$j], $autopay[$j], $day[$j], $paid[$j], $income[$j]);
    fputcsv($handle, $line);
}
fclose($handle);
echo "OK";
