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
require_once "../database/global_boot.php";
require_once "../utilities/timeSetup.php";

if ($_SESSION['cookies'] === 'accept') {
    $menu_item = 'Reject Cookies';
} else {
    $menu_item = 'Accept Cookies';
}
// if month has rolled over: get all current balances for each budget item
if ($rollover) {
    $getBals = "SELECT `id`,`prev0`,`prev1`,`current` FROM `Budgets` " .
        "WHERE `userid` = :uid;";
    $buddat = $pdo->prepare($getBals);
    $buddat->execute(["uid" => $_SESSION['userid']]);
    $all_accounts = $buddat->fetchALL(PDO::FETCH_ASSOC);
    $tblids = [];
    foreach ($all_accounts as &$acct) {
        array_push($tblids, $acct['id']);
        $acct['prev0'] = $acct['prev1'];
        $acct['prev1'] = $acct['current'];
    }
    for ($k=0; $k<count($all_accounts); $k++) {
        $putBals = "UPDATE `Budgets` SET `prev0` = :p0,`prev1` = :p1," .
            "`funded` = '0' WHERE `id` = :uid;";
        $newdat = $pdo->prepare($putBals);
        $newdat->execute(
            ["p0" => $all_accounts[$k]['prev0'], "p1" => $all_accounts[$k]['prev1'],
            "uid" => $tblids[$k]]
        );
    }
}
require_once "../utilities/getAccountData.php";
require_once "../utilities/getCards.php";
require_once "../utilities/getExpenses.php";

$nonmonthly = false;
/**
 * If there is a non-monthlies account, set the expected balances for all
 * based on payment frequency, current month, and payment history
 */ 
if (in_array('Non-Monthlies', $account_names)) {
    $nonmonthly = true;
    $nmfbal = getCurrentNMBal('funds', $pdo, $_SESSION['userid']);
    $nmebal = getCurrentNMBal('expected', $pdo, $_SESSION['userid']);
    setNMExpected($pdo, $_SESSION['userid'], $month_names, $thismo, $thisyear);
}
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
