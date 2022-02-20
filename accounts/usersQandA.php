<?php
/**
 * Produce the html for the Security Questions modal showing all the
 * questions and the users current answers
 * PHP Version 7.4
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();

require_once "../database/global_boot.php";
require "../accounts/security_questions.php";
chdir('../phpseclib1.0.20');
require "Crypt/RSA.php";
$publickey  = file_get_contents('../../budprivate/publickey.pem');
$rsa = new Crypt_RSA();
$rsa->loadKey($publickey);

// retrieve user's data
$user_qandaReq = "SELECT `questions`,`an1`,`an2`,`an3` FROM `Users` WHERE `uid`=?;";
$user_qanda = $pdo->prepare($user_qandaReq);
$user_qanda->execute([$_SESSION['userid']]);
$qadata = $user_qanda->fetch(PDO::FETCH_ASSOC);
$userqs = $qadata['questions'];
$a1cipher = hex2bin($qadata['an1']);
$a2cipher = hex2bin($qadata['an2']);
$a3cipher = hex2bin($qadata['an3']);
$qa[0] = $rsa->decrypt($a1cipher);
$qa[1] = $rsa->decrypt($a2cipher);
$qa[2] = $rsa->decrypt($a3cipher);
$uques = explode(",", $userqs);


// formulate modal body
$body = '';
$ansno = 0;
for ($k=0; $k<10; $k++) {
    $body .= '<span class="ques">' . $questions[$k] . '</span>';
    if (in_array($k, $uques)) {
        $body .= '<input id="q' . $k . '" type="text" name="ans[]" value="' .
            $qa[$ansno++] . '" /><br />';
    } else {
        $body .= '<input id="q' . $k . '" type="text" name="ans[]" /><br />';
    }
}
echo $body;
