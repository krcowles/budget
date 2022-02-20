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

chdir('../phpseclib1.0.20');
require "Crypt/RSA.php";
$rsa = new Crypt_RSA();
$privatekey  = file_get_contents('../../budprivate/privatekey.pem');
$rsa->loadKey($privatekey);

$ques_str = filter_input(INPUT_POST, 'questions');
$ans_str  = filter_input(INPUT_POST, 'answers');
$answers  = explode("|", $ans_str);

$cipher1 =  $rsa->encrypt($answers[0]);
$an1 = bin2hex($cipher1);
$cipher2 =  $rsa->encrypt($answers[1]);
$an2 = bin2hex($cipher2);
$cipher2 =  $rsa->encrypt($answers[2]);
$an3 = bin2hex($cipher2);

$UpdateReq = "UPDATE `Users` SET `questions`=?,`an1`=?,`an2`=?,`an3`=? " .
    " WHERE `uid`=?;";
$update = $pdo->prepare($UpdateReq);
$update->execute([$ques_str, $an1, $an2, $an3, $_SESSION['userid']]);

echo "ok";
