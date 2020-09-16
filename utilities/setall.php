<?php
/**
 * This utility simply sets the `setup` field in the `Users` db to 'done'.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require_once "../database/global_boot.php";

$done = "UPDATE `Users` SET `setup` = '111' WHERE `uid` = ?";
$updte = $pdo->prepare($done);
$updte->execute([$_SESSION['userid']]);
$_SESSION['start'] = '111';
