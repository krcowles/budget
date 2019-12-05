<?php
/**
 * This script updates the 'Budget' and 'Charges' tables to effect an
 * automatic payment designated by the user.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license
 */
$x = 1;
$user   = filter_input(INPUT_POST, 'user');
$apacct = filter_input(INPUT_POST, 'acct');
$apamt    = filter_input(INPUT_POST, 'amt');
$payee  = filter_input(INPUT_POST, 'payee');
$with   = filter_input(INPUT_POST, 'method');

require "getAccountData.php";
require "getCards.php";  // required by getExpenses.php
require "getExpenses.php";
require "timeSetup.php";

// update Budgets table
$acctkey = array_search($apacct, $account_names);
$tblid = $acctid[$acctkey];
$currval = floatval($current[$acctkey]);
$newval = $currval - floatval($apamt);
$newcurr = "UPDATE `Budgets` SET `current` = :val, `autopd` = :ap " .
    "WHERE `id` = :tblid;";
$updte = $pdo->prepare($newcurr);
$updte->execute(["val" => $newval, "ap" => $digits[0], "tblid" => $tblid]);

// update Charges table
if (in_array($with, $cr)) {
    $methodtype = 'Credit';
} else {
    $methodtype = 'Debit';
}
$newexp = "INSERT INTO `Charges` (`user`,`method`,`cdname`,`expdate`,`expamt`," .
    "`payee`,`acctchgd`,`paid`) VALUES ('" . $user ."','" . $methodtype . "','" .
    $with . "','" . $tbldate . "','" . $apamt . "','" . $payee .
    "','" . $apacct . "','N');";
$pdo->query($newexp);

echo "OK";
