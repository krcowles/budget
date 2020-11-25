<?php
/**
 * This script saves the user's cookie choice to the Users table
 * and registers the choice in the login session variables.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license
 */
session_start();
require "../database/global_boot.php";

$newchoice = filter_input(INPUT_GET, 'choice');

$updateReq = "UPDATE `Users` SET `cookies` = ? WHERE `uid` = ?;";
$newcookies = $pdo->prepare($updateReq);
$newcookies->execute([$newchoice, $_SESSION['userid']]);
