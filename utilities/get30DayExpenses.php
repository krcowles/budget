<?php
/**
 * This utility queries the 'Charges' table to extract paid checks/drafts
 * and debits (debit card) from the previous 30-day period.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require_once "../database/global_boot.php";
require "timeSetup.php";

$exp30Req = "SELECT * FROM `Charges` WHERE `userid` = :uid AND " .
        "`method` = 'Check';";
$chks = $pdo->prepare($exp30Req);
$chks->execute(["uid" => $_SESSION['userid']]);
$expenditures = $chks->fetchALL(PDO::FETCH_ASSOC);
$prev30 = time() - (30 * 24 * 60 * 60);
// collection of info for each qualified expense (<= previous 30 days)
$eid = [];
$dte = [];
$amt = [];
$pye = [];
$ded = [];
foreach ($expenditures as $pd) {
    $rel = strtotime($pd['expdate']);
    if ($rel >= $prev30) {
        array_push($eid, $pd['expid']);
        array_push($dte, $pd['expdate']);
        array_push($amt, $pd['expamt']);
        array_push($pye, $pd['payee']);
        array_push($ded, $pd['acctchgd']);
    }
}
$dbdata = "SELECT * FROM `Charges` WHERE `userid` = :uid AND `method` = 'Debit';";
$debs = $pdo->prepare($dbdata);
$debs->execute(["uid" => $_SESSION['userid']]);
$recent = $debs->fetchALL(PDO::FETCH_ASSOC);
$did = [];
$debname = [];
$dday = [];
$damt = [];
$dpay = [];
$dfrm = [];
foreach ($recent as $deb) {
    $rel = strtotime($deb['expdate']);
    if ($rel >= $prev30) {
        array_push($did, $deb['expid']);
        array_push($debname, $deb['cdname']);
        array_push($dday, $deb['expdate']);
        array_push($damt, $deb['expamt']);
        array_push($dpay, $deb['payee']);
        array_push($dfrm, $deb['acctchgd']);
    }
}
