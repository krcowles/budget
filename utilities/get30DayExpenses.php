<?php
/**
 * This utility queries the 'Charges' table to extract paid checks/drafts
 * and debits (debit card) from the previous 30-day period. Each gets sorted
 * by date (in ascending order).
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require_once "../database/global_boot.php";
require "timeSetup.php";

$prev30 = time() - (31 * 24 * 60 * 60);
$start_date = date('Y-m-d', $prev30);
/**
 * Check/draft expenses for the last 30 days
 */
$exp30Req = "SELECT * FROM `Charges` WHERE `userid` = :uid AND `method` = 'Check' " .
    "AND `expdate` BETWEEN '{$start_date}' AND '{$tbldate}';";
$chks = $pdo->prepare($exp30Req);
$chks->execute(["uid" => $_SESSION['userid']]);
$expenditures = $chks->fetchALL(PDO::FETCH_ASSOC);
// collection of info for each qualified expense (<= previous 30 days)
$eid = [];
$dte = [];
$amt = [];
$pye = [];
$ded = [];
// to establish sorting by date:
$date_field = [];
for ($i=0; $i<count($expenditures); $i++) {
    $indx = 'indx' . $i;
    $date_field[$indx] = $expenditures[$i]['expdate']; 
}
asort($date_field); // retains associative keys
$sort_keys = array_keys($date_field);
$date_order = [];
foreach ($sort_keys as $itemno) {
    $item_no = intval(substr($itemno, 4));
    array_push($date_order, $item_no); 
}
// $date_order is the sorted index for all charges
for ($j=0; $j<count($expenditures); $j++) {
    $indx = $date_order[$j];
    array_push($eid, $expenditures[$indx]['expid']);
    array_push($dte, $expenditures[$indx]['expdate']);
    array_push($amt, $expenditures[$indx]['expamt']);
    array_push($pye, $expenditures[$indx]['payee']);
    array_push($ded, $expenditures[$indx]['acctchgd']);
}

/**
 * Debit Card expenses for the last 30 days
 */
$dbdata = "SELECT * FROM `Charges` WHERE `userid` = :uid AND `method` = 'Debit' " .
    "AND `expdate` BETWEEN '{$start_date}' AND '{$tbldate}';";
$debs = $pdo->prepare($dbdata);
$debs->execute(["uid" => $_SESSION['userid']]);
$recent = $debs->fetchALL(PDO::FETCH_ASSOC);
$did = [];
$debname = [];
$dday = [];
$damt = [];
$dpay = [];
$dfrm = [];
// for sorting by date
$dsort = [];
for ($i=0; $i<count($recent); $i++) {
    $indx = 'indx' . $i;
    $dsort[$indx] = $recent[$i]['expdate']; 
}
asort($dsort); // retains associative keys
$dkeys = array_keys($dsort);
$dc_order = [];
foreach ($dkeys as $ino) {
    $item_no = intval(substr($ino, 4));
    array_push($dc_order, $item_no); 
}
for ($k=0; $k<count($recent); $k++) {
    $indx = $dc_order[$k];
    array_push($did, $recent[$indx]['expid']);
    array_push($debname, $recent[$indx]['cdname']);
    array_push($dday, $recent[$indx]['expdate']);
    array_push($damt, $recent[$indx]['expamt']);
    array_push($dpay, $recent[$indx]['payee']);
    array_push($dfrm, $recent[$indx]['acctchgd']);
}
