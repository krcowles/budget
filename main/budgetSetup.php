<?php
/**
 * This script acquires the data needed to create the main budget table displayed
 * to the user.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license
 */
require "../utilities/getAccountData.php";
require "../utilities/getCards.php";
require "../utilities/getExpenses.php";
require "../utilities/timeSetup.php";

// form balances
$balBudget  = 0;
$balPrev0   = 0;
$balPrev1   = 0;
$balCurrent = 0;
foreach ($budgets as $item) {
    $balBudget += intval($item);
}
foreach ($prev0 as $item) {
    $balPrev0 += floatval($item);
}
foreach ($prev1 as $item) {
    $balPrev1 += floatval($item);
}
foreach ($current as $item) {
    $balCurrent += floatval($item);
}
// add credit card charges to $balCurrent (which only has account balance so far)

foreach ($cardbal as $cdarray) {
    $balCurrent += floatval($cdarray['bal']);
}

// format dollar items for the table
for ($a=0; $a<count($account_names); $a++) {
    $budgets[$a] = dataPrep($budgets[$a], 'budget');
    $prev0[$a]   = dataPrep($prev0[$a], 'prev0');
    $prev1[$a]   = dataPrep($prev1[$a], 'prev1');
    $current[$a] = dataPrep($current[$a], 'current');
}
for ($b=0; $b<count($cardbal); $b++) {
    $cardbal[$b]['bal'] = dataPrep($cardbal[$b]['bal'], 'card');
}
$balBudget  = dataPrep($balBudget, 'budget');
$balPrev0   = dataPrep($balPrev0, 'prev0');
$balPrev1   = dataPrep($balPrev1, 'prev1');
$balCurrent = dataPrep($balCurrent, 'current');
