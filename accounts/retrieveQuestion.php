<?php
/**
 * Select a login security question. This script is ajaxed.
 * The irregular processing for 'olduser' will be removed once the oldusers
 * have selected security questions.
 * PHP Version 7.4
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();

require_once "../database/global_boot.php";
require "../accounts/security_questions.php";

$userid = filter_input(INPUT_POST, 'ix');

$getQReq = "SELECT `questions` FROM `Users` WHERE `uid`=?;";
$getQ = $pdo->prepare($getQReq);
$getQ->execute([$userid]);
$Qstring = $getQ->fetch(PDO::FETCH_ASSOC);
$indx = rand(0, 2);
$Qnos = explode(",", $Qstring['questions']);
$ranQ = $questions[$Qnos[$indx]];
$ques_data = array('ques' => $ranQ, 'rindx' => $indx);
echo json_encode($ques_data);
