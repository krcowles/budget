<?php
/**
 * This script is to be included if the user has a Non-Monthlies account.
 * PHP Version 8.3.9
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
$nonmonthly = true;
$nm_indx = array_search('Non-Monthlies', $account_names);
$nm_bal  = floatval($current[$nm_indx]);
// collect autopay info
$apacct  = [];
$aptype  = [];
$apday   = [];
$apnext  = [];
$getNM_Data = "SELECT `record`,`item`,`freq`,`amt`,`first`,`SA_yr`,`APType`," .
    "`APDay`,`mo_pd`,`yr_pd` FROM `Irreg` WHERE `userid`=?;";
$NM_Data = $pdo->prepare($getNM_Data);
$NM_Data->execute([$_SESSION['userid']]);
$nmdata = $NM_Data->fetchAll(PDO::FETCH_ASSOC);
$sortByDueDate = [];
$autopays = [];
// extract data needed for sorting and processing of non-monthly accts
foreach ($nmdata as $data) {
    // keep track of which records are autopays
    if (!empty($data['APType'])) {
        array_push($autopays, $data['record']);
    }
    $month_data = prepNonMonthly(
        $data['freq'], $data['first'], $data['amt'], $data['SA_yr'],
        $data['mo_pd'], intval($data['yr_pd']), $month_names, $thismo,
        $thisyear, $data['record']
    );
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

foreach ($sorted as $data) {
    // fund each account by priority (due date)
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
    // if autopay, get info for budget.js
    if (in_array($data[4], $autopays)) {
        // has this autopay already been paid?
        if (!$data[0] && $data[1] === $thisyear) { // not paid yet
            $key = 0;
            for ($j=0; $j<count($nmdata); $j++) {
                if ($data[4] === $nmdata[$j]['record']) {
                    $key = $j;
                    break;
                }
            }
            array_push($apacct, $nmdata[$key]['item']);
            array_push($aptype, $nmdata[$key]['APType']);
            array_push($apday,  $nmdata[$key]['APDay']);
            array_push($apnext, $data[2]);
        }
    }
}
// 1. This month's non-monthlies balances
$nmfbal = getCurrentNMBal('funds', $pdo, $_SESSION['userid']);
$nmebal = getCurrentNMBal('expected', $pdo, $_SESSION['userid']);
// 2. Present autopay data to javascript on displayBudget.php
$js_nmapacct = json_encode($apacct);
$js_nmaptype = json_encode($aptype);
$js_nmapdays = json_encode($apday);
$js_nmapdues = json_encode($apnext);
