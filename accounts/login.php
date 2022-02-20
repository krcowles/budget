<?php
/**
 * This script is invoked when a user has been authenticated, and has 
 * also correctly anwered the associated security question.
 * PHP Version 7.4
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require "../database/global_boot.php";

$user = filter_input(INPUT_POST, 'ix');

$getUserReq = "SELECT * FROM `Users` WHERE `uid`=?;";
$getUser = $pdo->prepare($getUserReq);
$getUser->execute([$user]);
$user_dat = $getUser->fetch(PDO::FETCH_ASSOC);

$_SESSION['userid']       = $user;
$_SESSION['cookies']      = $user_dat['cookies'];
$_SESSION['start']        = $user_dat['setup'];
echo "OK";
