<?php
/**
 * This script acquires the data needed to create the main budget table displayed
 * to the user.
 * PHP Version 8.3.9
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license
 */
require_once "../database/global_boot.php";
require_once "../utilities/timeSetup.php";

$menu_item = $_SESSION['cookies'] === 'accept' ? 'Reject Cookies' : 'Accept Cookies';
// if month has rolled over: get all current balances for each budget item
if ($rollover) {
    $getBals = "SELECT `id`,`prev0`,`prev1`,`current` FROM `Budgets` " .
        "WHERE `userid` = :uid;";
    $buddat = $pdo->prepare($getBals);
    $buddat->execute(["uid" => $_SESSION['userid']]);
    $all_accounts = $buddat->fetchALL(PDO::FETCH_ASSOC);
    $tblids = [];
    foreach ($all_accounts as &$acct) {
        array_push($tblids, $acct['id']); // id = table id
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
    $nm_indx = array_search('Non-Monthlies', $account_names);
    $nm_bal  = floatval($current[$nm_indx]);
    // collect autopay info
    $apacct  = [];
    $aptype  = [];
    $apday   = [];
    $apnext  = [];
     /**
     * Redistribute current available $nm_bal into non-monthly accounts;
     */
    $getNM_Data = "SELECT `record`,`item`,`freq`,`amt`,`first`,`SA_yr`,`APType`," .
        "`APDay`,`mo_pd`,`yr_pd` FROM `Irreg` WHERE `userid`=?;";
    $NM_Data = $pdo->prepare($getNM_Data);
    $NM_Data->execute([$_SESSION['userid']]);
    $nmdata = $NM_Data->fetchAll(PDO::FETCH_ASSOC);
    $sortByDueDate = [];
    // extract data needed for sorting and processing of non-monthly accts
    foreach ($nmdata as $data) {
        $month_data = prepNonMonthly(
            $data['freq'], $data['first'], $data['amt'], $data['SA_yr'],
            $data['mo_pd'], intval($data['yr_pd']), $month_names, $thismo,
            $thisyear, $data['record']
        );
        // add in record no for recording results later...
        array_push($sortByDueDate, $month_data);
    }
    // any 'next_due' months earlier than the current mo are for next year:
    $next_yr = [];
    $two_yrs = [];
    $removals = [];
    for ($j=0; $j<count($sortByDueDate); $j++) {
        if ($sortByDueDate[$j][1] === $thisyear + 1) {
            array_push($next_yr, $sortByDueDate[$j]);
            array_push($removals, $j);
        } elseif ($sortByDueDate[$j][1] === $thisyear + 2) {
            array_push($two_yrs, $sortByDueDate[$j]);
            array_push($removals, $j);
        }
    }
    // remove any due dates scheduled for a later year
    if (count($removals) > 0) {
        foreach ($removals as $postpone) {
            unset($sortByDueDate[$postpone]);
        }
    }
    // sort by dates two years ahead, if needed:
    if (count(value: $two_yrs) > 1) {
        usort($two_yrs, 'compareNextDue');
    }
    // sort by next year's due date if needed:
    if (count($next_yr) > 1) {
        usort($next_yr, 'compareNextDue');
    }
    // sort the 'current year' due dates
    usort($sortByDueDate, 'compareNextDue');
    // add in next year's sorted due dates
    $sorted = array_merge($sortByDueDate, $next_yr, $two_yrs);
    // process the data
    foreach ($sorted as $data) {
        if (!empty($data['APType'])) {  // collect autopay data
            if (!$data[0] && !$data[1]) { // not paid yet
                array_push($apacct, $data['item']);
                array_push($aptype, $data['APType']);
                array_push($apday,  $data['APDay']);
                array_push($apnext, $data[2]);
            }
        }
        $expected = $data[3];
        $funding = 0;
        if ($nm_bal > 0) {
            if ($nm_bal >= $expected) {
                // if funds are available, set accumulated funds = expected
                $funding = $expected;
                $nm_bal -= $funding;
            } else {
                $funding = $nm_bal;
                $nm_bal = 0;
            }   
        }
        $updateExpReq = "UPDATE `Irreg` SET `expected`=?,`funds`=? WHERE " .
            "`record`=?;";
        $updateExp = $pdo->prepare($updateExpReq);
        $updateExp->execute([$expected, $funding, $data[4]]);
        //$updateFundsReq = "UPDATE `Irreg` SET `funds`=? WHERE `record`=?;";
    }
    if ($nm_bal > 0) {
        // place any excess into undistributed funds
        $undis = array_search("Undistributed Funds", $account_names);
        $current[$undis] += $nm_bal;
        $incUndisReq = "UPDATE `Budgets` SET `current`=? WHERE `userid`=? " .
            "AND `budname`='Undistributed Funds';";
        $incUndis = $pdo->prepare($incUndisReq);
        $incUndis->execute([$current[$undis], $_SESSION['userid']]);
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
