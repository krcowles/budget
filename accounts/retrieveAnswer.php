<?php
/**
 * Retrieve the user's saved security question response. This script is ajaxed.
 * PHP Version 7.4
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();

require_once "../database/global_boot.php";

$uid = filter_input(INPUT_POST, 'ix');
$idx = filter_input(INPUT_POST, 'rx');

$retAnsReq = "SELECT `answers` FROM `Users` WHERE `uid`=?;";
$retAns = $pdo->prepare($retAnsReq);
$retAns->execute([$uid]);
$allAns = $retAns->fetch(PDO::FETCH_ASSOC);
$uanswers = explode(",", $allAns['answers']);
echo $uanswers[$idx];
