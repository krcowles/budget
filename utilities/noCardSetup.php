<?php
/**
 * The user has chosen to not enter credit cards during setup. Mark the 
 * setup field in the Users table accordingly for the user
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
$user = filter_input(INPUT_POST, 'usr');

require_once "../database/global_boot.php";
$oldsetup = "SELECT `setup` FROM `Users` WHERE `username` = :uid;";
$old = $pdo->prepare($oldsetup);
$old->execute(["uid" => $user]);
$fetched = $old->fetch(PDO::FETCH_ASSOC);
$setup = $fetched['setup'] |  '011';
$status = "UPDATE `Users` SET `setup` = :setup WHERE `username` = :uid;";
$newstat = $pdo->prepare($status);
$newstat->execute(["setup" => $setup, "uid" => $user]);
