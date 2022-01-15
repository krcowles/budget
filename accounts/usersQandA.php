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

// retrieve user's data
$user_qandaReq = "SELECT `questions`,`answers` FROM `Users` WHERE `uid`=?;";
$user_qanda = $pdo->prepare($user_qandaReq);
$user_qanda->execute([$_SESSION['userid']]);
$qadata = $user_qanda->fetch(PDO::FETCH_ASSOC);
$userqs = $qadata['questions'];
$useras = $qadata['answers'];
$uques = explode(",", $userqs);
$uans  = explode(",", $useras);

// formulate modal body
$body = '';
$ansno = 0;
for ($k=0; $k<10; $k++) {
    $body .= '<span class="ques">' . $questions[$k] . '</span>';
    if (in_array($k, $uques)) {
        $body .= '<input id="q' . $k . '" type="text" name="ans[]" value="' .
            $uans[$ansno++] . '" /><br />';
    } else {
        $body .= '<input id="q' . $k . '" type="text" name="ans[]" /><br />';
    }
}
echo $body;
