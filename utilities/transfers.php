<?php
/**
 * This module simply stores the transfer executed by the user for
 * the purpose of creating an on-demand report
 * PHP Version 7.4
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require "../database/global_boot.php";

$user   = $_SESSION['userid'];
$from   = filter_input(INPUT_POST, 'from');
$to     = filter_input(INPUT_POST, 'to');
$amount = filter_input(INPUT_POST, 'amt');
$time   = date("Y-m-d");

$saveXfrReq = "INSERT INTO `Transfers` (`userid`,`from_acct`,`to_acct`,`amount`," .
    "`xfr_date`) VALUES (?,?,?,?,?);";
$saveXfr = $pdo->prepare($saveXfrReq);
$saveXfr->execute([$user, $from, $to, $amount, $time]);

echo "ok";
