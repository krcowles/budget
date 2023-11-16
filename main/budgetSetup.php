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
 * If there is a non-monthlies account perform the following activities:
 * NOTE: Every non-monthly account will have a cycle of payment months
 *   1. Retrieve data to calculate the current funds available and the
 *      funds expected to meet the month's needs (info available to user);
 *   2. Extract any data pertinent to autopays and present to displayBudget.php
 *      javascript (budget.js)
 */ 
if (in_array('Non-Monthlies', $account_names)) {
    $nonmonthly = true;
    $apacct   = [];
    $aptype   = [];
    $apday    = [];
    $apnext   = [];
    $getNM_Data = "SELECT `record`,`item`,`freq`,`amt`,`first`,`SA_yr`,`APType`," .
        "`APDay`,`mo_pd`,`yr_pd` FROM `Irreg` WHERE `userid`=?;";
    $NM_Data = $pdo->prepare($getNM_Data);
    $NM_Data->execute([$_SESSION['userid']]);
    $nmdata = $NM_Data->fetchAll(PDO::FETCH_ASSOC);
    foreach ($nmdata as $data) {
        $month_data = prepNonMonthly(
            $data['freq'], $data['first'], $data['amt'], $data['SA_yr'], 
            $data['mo_pd'], intval($data['yr_pd']), $month_names, $thismo, $thisyear
        );
        if (!empty($data['APType'])) {  // collect autopay data
            if (!$month_data[0] && !$month_data[1]) { // not paid yet
                array_push($apacct, $data['item']);
                array_push($aptype, $data['APType']);
                array_push($apday,  $data['APDay']);
                array_push($apnext, $month_data[2]);
            }
        }
        $updateExpReq = "UPDATE `Irreg` SET `expected`=? WHERE `record`=?;";
        $updateExp = $pdo->prepare($updateExpReq);
        $updateExp->execute([$month_data[3], $data['record']]);
    }
    // 1. This month's balances
    $nmfbal = getCurrentNMBal('funds', $pdo, $_SESSION['userid']);
    $nmebal = getCurrentNMBal('expected', $pdo, $_SESSION['userid']);
    // 2. Present autopay data to javascript on displayBudget.php
    $js_nmapacct = json_encode($apacct);
    $js_nmaptype = json_encode($aptype);
    $js_nmapdues = json_encode($apnext);
    $js_nmapdays = json_encode($apday);
}

// form budget balances
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
