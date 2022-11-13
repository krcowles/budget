<?php
/**
 * This script uses the email address provided by the user and verifies its
 * existence in the 'Users' table. If present, an email is sent to the user
 * providing the user the account User Name and a temporary passcode to allow
 * the user to set/reset the account password.
 * NOTE: Doesn't work on localhost!
 * PHP Version 7.4
 * 
 * @package MedRefs
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../php/global_boot.php";
require "../accounts/gmail.php";
verifyAccess('ajax');
/*
 * For cases where username is encrypted:
chdir('../phpseclib1.0.20');
require "Crypt/RSA.php";
$publickey  = file_get_contents('../../medprivate/publickey.pem');
$rsa = new Crypt_RSA();
 */

$new_registration = isset($_POST['reg']) ? true : false;
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
if (!$email) {
    echo "bad";
    exit;
}

// text for message body
$msg = <<<LNK
<h3>Do not reply to this message</h3>
<p>Your MedRefs account is locked until you reset your password.<br />
Your User Name is:
LNK;
$newreg = <<<REG
<h3>Do not reply to this message</h3>
<p>You have a new account with MedRefs for
User Name: 
REG;

$href  = '<a href="' . SITE_URL . '/accounts/unifiedLogin.php?';
$href .= "form=pwd_reset&code=";

// security code:
$tmp_pass = bin2hex(random_bytes(5)); // 10 hex characters
$hash = password_hash($tmp_pass, PASSWORD_DEFAULT);
$registered
    = $pdo->query('SELECT email FROM `Users`;')->fetchAll(PDO::FETCH_COLUMN);
if (in_array($email, $registered)) {
    // get username
    $udatReq = "SELECT `userid`,`username` FROM `Users` WHERE `email` = :email;";
    $udat = $pdo->prepare($udatReq);
    $udat->execute(["email" => $email]);
    $user_data = $udat->fetch(PDO::FETCH_ASSOC);
    $uid = $user_data['userid'];
    //$rsa->loadKey($publickey);
    $uname = $user_data['username'];
    //$newcipher = hex2bin($uname);
    //$orgname = $rsa->decrypt($newcipher);
    // save temporary secure password
    $savecodeReq = "UPDATE `Users` SET `passwd` = ? WHERE `userid` = ?;";
    $savecode = $pdo->prepare($savecodeReq);
    $savecode->execute([$hash, $uid]);
} else {
    echo "nofind";
    exit;
}
if (!$new_registration) { // Renew, change, or forgot password 
    $msg .= ' ' . $uname . '</p>'; // use orgname for encrypted user names
    $msg .= '<p>A temporary passcode has been assigned; You will not be able ' .
        'to use this code to login: ';
    $msg .= '<strong>' . $tmp_pass . '</strong></p>';
    $href .= $tmp_pass . '&ix=' . $uid .
        '"><span style="font-size:16px;">Click to reset your password</span></a>';
    $msg .= $href;
    $user_msg = $msg;
    $subject = "Password Reset";
} else { // New registration (use $orgname for encrypted usernames)
    $newreg .= $uname . '<br />You will not be able to login until you ' .
        'complete your registration by clicking the link below. ' .
        'A temporary passcode has been assigned ' . $tmp_pass;
    $newreg .= '<br /><br />' . $href . $tmp_pass . '&ix=' . $uid .
        '&reg=y">New Account Link</a>';
    $user_msg = $newreg;
    $subject = "New Account";
}
$mail->isHTML(true);
$mail->setFrom('webmaster@medrefs.com', 'Do not reply');
$mail->addAddress($email, 'MedRefs User');
$mail->Subject = $subject;
$mail->Body = $user_msg;
if (!$mail->send()) {
    $errmsg = $mail->ErrorInfo;
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'ok';
}
