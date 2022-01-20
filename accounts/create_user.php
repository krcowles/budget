<?php
/**
 * This script will update the Users table with the form information entered by
 * the new user on Registration.html, or update it for account renewal.
 * THis script is called by ajax.
 * PHP Version 7.4
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require_once "../database/global_boot.php";
require "../accounts/accountFunctions.php";

// RSA encryption:
chdir("../phpseclib1.0.20");
require "Crypt/RSA.php";
$rsa = new Crypt_RSA();
$privatekey  = file_get_contents('../database/prclasskey.txt');
$rsa->loadKey($privatekey);

$submitter = filter_input(INPUT_POST, 'submitter');
// response is based on caller [$submitter]:
if ($submitter == 'create') {
    // 'create': Registration: enter minimal data, complete later via email link
    $email  = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    if ($email === false) {
        echo "bademail";
        exit;
    }
    $username  = filter_input(INPUT_POST, 'username');
    // encrypt username
    $ciphertext = $rsa->encrypt($username);
    $dbuser = bin2hex($ciphertext);
    // starting month
    $today = getdate();
    $month_string = $today['month'];
    // save first portion of user data
    $newuser = "INSERT INTO `Users` (`email`,`username`,`setup`,`LCM`) " .
        "VALUES (:email,:uname,'000','{$month_string}');";
    $user = $pdo->prepare($newuser);
    $user->execute(
        array( ":email" => $email, ":uname" =>  $dbuser)
    );
} elseif ($submitter == 'change') {
    // 'change': New registrant or update of password (forgot/change/renew)
    $today = getdate();
    $month_digits = $today['mon'];
    $month_string = $today['month'];
    $day = $today['mday'];
    $year = intval($today['year']);
    $year++;
    $exp_date = $year . "-" . $month_digits . "-" . $day;

    $user_pass = filter_input(INPUT_POST, 'password');
    $password  = password_hash($user_pass, PASSWORD_DEFAULT);
    $choice    = filter_input(INPUT_POST, 'cookies');
    $questions = filter_input(INPUT_POST, 'ques');
    $answers   = filter_input(INPUT_POST, 'answ');
    $user      = filter_input(INPUT_POST, 'userid');
    $tmp_code  = filter_input(INPUT_POST, 'oldpass');
    if (!empty($tmp_code)) {
        /**
         * This represents the case where either a registration is completing,
         * or a change/forgot password is being processed [ie NOT 'Renew']
         * Verify the one-time code before proceeding
         */
        $baseDatReq
            = "SELECT * FROM `Users` WHERE `uid`=?;";
        $baseDat = $pdo->prepare($baseDatReq);
        $baseDat->execute([$user]);
        $user_dat = $baseDat->fetch(PDO::FETCH_ASSOC);
        if (!password_verify($tmp_code, $user_dat['password'])) {
            echo "NOTFOUND";
            exit;
        }
        $uid = $user;
        // in case this is a reset from a lockout:
        $lockTable
            = $pdo->query("SHOW TABLES LIKE 'Locks';")->fetchAll(PDO::FETCH_NUM);
        if (count($lockTable) > 0) {
            $ip = getIpAddr();
            $ip = $ip === '::1' ? '127.0.0.1' : $ip;  // accomodate Chrome localhost
            $loCheckReq = "SELECT * FROM `Locks` WHERE `ipaddr`=?;";
            $lockCheck = $pdo->prepare($loCheckReq);
            $lockCheck->execute([$ip]);
            $locks = $lockCheck->fetchAll(PDO::FETCH_ASSOC);
            if (count($locks) > 0) {
                $killitReq = "DELETE FROM `Locks` WHERE `ipaddr`=?;";
                $killit = $pdo->prepare($killitReq);
                $killit->execute([$ip]);
            }
        }
    } else { // a renewal process
        $uid = $user == '0' ? $_SESSION['userid'] : $user;
    }
    // encrypt answers
    $dbans = [];
    $ans_array = explode("|", $answers);
    foreach ($ans_array as $answer) {
        $cipher_answer = $rsa->encrypt($answer);
        $hex_answer = bin2hex($cipher_answer);
        array_push($dbans, $hex_answer);
    }
    $updateuser = "UPDATE `Users` SET `password`=?,`passwd_expire`=?," .
        "`cookies`=?,`questions`=?,`answers`=?,`an1`=?,`an2`=?,`an3`=? ".
        "WHERE `uid`=?;";
    $update = $pdo->prepare($updateuser);
    $update->execute(
        array(
            $password,
            $exp_date,
            $choice,
            $questions,
            $answers,
            $dbans[0],
            $dbans[1],
            $dbans[2],
            $uid)
    );
    if ($choice === 'accept') {
        // need username
        $getUserReq = "SELECT `username` FROM `Users` WHERE `uid`=?;";
        $getUser = $pdo->prepare($getUserReq);
        $getUser->execute([$user]);
        $username = $getUser->fetch(PDO::FETCH_ASSOC);
        $uxtime = (365 * 24 * 60 * 60) + time(); // currently 1 year expiration
        setcookie("mybud", $username['username'], $uxtime, "/", "", false, true);
    }
}
echo "DONE";
