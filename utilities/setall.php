<?php
/**
 * This utility simply sets the `setup` field in the `Users` db to 'done'.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require_once "../database/global_boot.php";
$user = filter_input(INPUT_POST, 'user');

$done = "UPDATE `Users` SET `setup` = '111' WHERE `username` = :uid;";
$updte = $pdo->prepare($done);
$updte->execute(["uid" => $user]);
