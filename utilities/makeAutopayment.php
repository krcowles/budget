<?php
/**
 * This script updates the 'Budget', 'Irreg', and 'Charges' tables
 * to effect an automatic payment designated by the user.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license
 */
session_start();
require "../database/global_boot.php";

$apacct = filter_input(INPUT_POST, 'acct');
$apamt  = filter_input(INPUT_POST, 'amt');
$payee  = filter_input(INPUT_POST, 'payee');
$with   = filter_input(INPUT_POST, 'method');
$forbud = filter_input(INPUT_POST, 'acctype');
$appay  = floatval($apamt);

require "getAccountData.php";
require "getCards.php";  // required by getExpenses.php
require "getExpenses.php";
require "timeSetup.php";

if ($forbud === 'bud') {
    // update Budgets table only
    $acctkey = array_search($apacct, $account_names);
    $tblid = $acctid[$acctkey];
    $currval = floatval($current[$acctkey]);
    $newval = $currval - $appay;
    $newcurr = "UPDATE `Budgets` SET `current` = :val, `autopd` = :ap " .
        "WHERE `id` = :tblid;";
    $updte = $pdo->prepare($newcurr);
    $updte->execute(["val" => $newval, "ap" => $digits[0], "tblid" => $tblid]);
} else { // Non-Monthlies
    // First, update 'Irreg'
    $getNMdat = "SELECT `record`,`item`,`funds` FROM `Irreg` WHERE " .
        "`APType` <> '' AND `userid`=?;";
    $NMdat = $pdo->prepare($getNMdat);
    $NMdat->execute([$_SESSION['userid']]);
    $NMauto = $NMdat->fetchAll(PDO::FETCH_ASSOC);
    foreach ($NMauto as $acct) {
        if ($acct['item'] == $apacct) {
            $newval = floatval($acct['funds']) - $appay;
            $recno = $acct['record'];
            break;
        }
    }
    $newamt = "UPDATE `Irreg` SET `funds`=?,`mo_pd`=?,`yr_pd`=?  WHERE " .
        "`record`=?;";
    $updte = $pdo->prepare($newamt);
    $updte->execute([$newval, $current_month, $thisyear, $recno]);
    // Next, update Budgets
    $getNMacctReq = "SELECT `current` FROM `Budgets` WHERE " .
        "`budname`='Non-Monthlies' AND `userid`=?;";
    $getNMacct = $pdo->prepare($getNMacctReq);
    $getNMacct->execute([$_SESSION['userid']]);
    $budAmt = $getNMacct->fetch(PDO::FETCH_ASSOC);
    $newAmt = floatval($budAmt['current']) - $appay;
    $updteBudReq = "UPDATE `Budgets` SET `current`=? WHERE " .
        "`budname`='Non-Monthlies' AND `userid`=?;";
    $updteBud = $pdo->prepare($updteBudReq);
    $updteBud->execute([$newAmt, $_SESSION['userid']]);
    $apacct = 'Non-Monthlies';
}
// Finally, update 'Charges'
if (in_array($with, $cr)) {
    $methodtype = 'Credit';
    $pd = 'N';
} else {
    $methodtype = 'Debit';
    $pd = 'Y';
}
$newexp = "INSERT INTO `Charges` (`userid`,`method`,`cdname`,`expdate`,`expamt`," .
    "`payee`,`acctchgd`,`paid`) VALUES ('" . $_SESSION['userid'] ."','" .
    $methodtype . "','" . $with . "','" . $tbldate . "','" . $apamt . "','" .
    $payee . "','" . $apacct . "','" . $pd . "');";
$pdo->query($newexp);

echo "OK";
