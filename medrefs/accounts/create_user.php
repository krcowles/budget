<?php
/**
 * This script will update the Users table with the form information entered by
 * the new user on Registration.html, or update it for account renewal.
 * THis script is invoked by ajax.
 * PHP Version 7.4
 * 
 * @package MedRefs
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require "../php/global_boot.php";
require "../accounts/accountFunctions.php";
verifyAccess('ajax');

/* Enable for username encryption...
chdir("../phpseclib1.0.20");
require "Crypt/RSA.php";
$rsa = new Crypt_RSA();
$privatekey  = file_get_contents(RSA_KEYS . '/privatekey.pem');
$rsa->loadKey($privatekey);
 */

$submitter = filter_input(INPUT_POST, 'submitter');
$choice = '';
// response is based on caller [$submitter]:
if ($submitter == 'create') {
    // 'create': Registration: enter minimal data, complete later via email link
    $email  = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    if ($email === false) {
        echo "bademail";
        exit;
    }
    $username  = filter_input(INPUT_POST, 'username');
    $firstname = filter_input(INPUT_POST, 'firstname');
    $lastname  = filter_input(INPUT_POST, 'lastname');
    /* --- FOR ENCRYPTING USERNAME ---
    // encrypt username
    $ciphertext = $rsa->encrypt($username);
    $dbuser = bin2hex($ciphertext);
    */
    $dbuser = $username; // comment out if encrypting
    // starting month
    $today = getdate();
    $month_string = $today['month'];
    // save first portion of user data
    $newuser = "INSERT INTO `Users` (`email`,`username`,`first_name`,`last_name`) " .
        "VALUES (:email,:uname,:fname,:lname);";
    $user = $pdo->prepare($newuser);
    $user->execute(
        array(
            ":email" => $email, ":uname" =>  $dbuser, ":fname" => $firstname,
            ":lname" => $lastname
        )
    );
    // Retrieve newly-created id:
    $lastIdReq = "SELECT `userid` FROM `Users` ORDER BY `userid` DESC LIMIT 1;";
    $lastId = $pdo->query($lastIdReq)->fetch(PDO::FETCH_ASSOC);
    $uid = $lastId['userid'];
    // use id to create the default settings
    $defaultReq = "INSERT INTO `Settings` (`userid`,`menu`,`active`) VALUES " .
        "(?,?,?);";
    $defaultSettings = $pdo->prepre($defaultReq);
    $defaultSettings->execute(
        [$uid, "My Doctors|Medications|Emergency Contacts", 1]
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
    $user      = filter_input(INPUT_POST, 'userid');
    $tmp_code  = filter_input(INPUT_POST, 'code');
       
    $baseDatReq
        = "SELECT * FROM `Users` WHERE `userid`=?;";
    $baseDat = $pdo->prepare($baseDatReq);
    $baseDat->execute([$user]);
    $user_dat = $baseDat->fetch(PDO::FETCH_ASSOC);
    if (!password_verify($tmp_code, $user_dat['passwd'])) {
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
    $updateuser = "UPDATE `Users` SET `passwd`=?,`passwd_expire`=?," .
        "`cookies`=? WHERE `userid`=?;";
    $update = $pdo->prepare($updateuser);
    $update->execute(
        array(
            $password,
            $exp_date,
            $choice,
            $uid)
    );
    if ($choice === 'accept') {
        // assume not RSA encrypted, or else using RSA for cookie...
        $getUserReq = "SELECT `username` FROM `Users` WHERE `userid`=?;";
        $getUser = $pdo->prepare($getUserReq);
        $getUser->execute([$user]);
        $username = $getUser->fetch(PDO::FETCH_ASSOC);
        $uxtime = (365 * 24 * 60 * 60) + time(); // currently 1 year expiration
        setcookie(SITE_REF, $username['username'], $uxtime, "/", "", false, true);
    }
}
echo "DONE";
