<?php
/**
 * This script will set the browser cookie when a user logins in and
 * is authenticated, assuming they have designated "accept cookies".
 * PHP Version 7.8
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../database/global_boot.php";

$user = filter_input(INPUT_POST, 'ix');

$getUnameReq = "SELECT `username` FROM `Users` WHERE `uid`=?;";
$getUname = $pdo->prepare($getUnameReq);
$getUname->execute([$user]);
$uname = $getUname->fetch(PDO::FETCH_ASSOC);
$username = $uname['username'];
$days = 365; // Number of days before cookie expires
$expire = time()+60*60*24*$days;
setcookie("mybud", $username, $expire, "/", "", false, true);
