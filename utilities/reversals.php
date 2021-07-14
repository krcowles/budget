<?php
/**
 * This module is called via ajax from reverseCharge.js or undoExpense.js.
 * It will operate on any data passed back from that page to 'reverse' either
 * one or more credit cards charges or one or more expenses. In either case,
 * the item is deleted from the 'Charges' Table, and the item's account is
 * incremented by the amount of the charge/expense.
 * PHP Version 7.1
 *
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require_once "../database/global_boot.php";

// The posted data will be received as an array of associative arrays
$undos = $_POST['rems'];
foreach ($undos as $reverse) {
    $acct = filter_var($reverse['acct']);
    $amt  = filter_var($reverse['amt']);
    $exid = filter_var($reverse['id']);
    $delReq = "DELETE FROM `Charges` WHERE `expid`=?;";
    $del = $pdo->prepare($delReq);
    $del->execute([$exid]);
    $revReq = "UPDATE `Budgets` SET `current`=`current` + {$amt} WHERE " .
        "`userid` = :uid AND `budname` = :acct AND " .
        "(`status` = 'A' OR `status`='T');";
    $rev = $pdo->prepare($revReq);
    $rev->execute(["uid" => $_SESSION['userid'], "acct" => $acct]);
}
