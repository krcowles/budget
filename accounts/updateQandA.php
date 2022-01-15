<?php
/**
 * When a user modifies (or simply reviews) the security questions via the
 * 'MyAccount->Update Sec. Questions' submenu, the questions list and 
 * corresponding answers are updated in the Users table.
 * PHP Version 7.4
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();

require_once "../database/global_boot.php";

$ques_str = filter_input(INPUT_POST, 'questions');
$ans_str  = filter_input(INPUT_POST, 'answers');

$UpdateReq = "UPDATE `Users` SET `questions`=?,`answers`=? WHERE `uid`=?;";
$update = $pdo->prepare($UpdateReq);
$update->execute([$ques_str, $ans_str, $_SESSION['userid']]);

echo "ok";
