<?php
/**
 * This script uses the email address provided by the user and verifies its
 * existence in the 'Users' table. If present, an email is sent to the user
 * providing the user the account User Name and a temporary password to reset
 * the account.
 * PHP Version 7.4
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../database/global_boot.php";
require "gmail.php";
chdir('../phpseclib1.0.20');
require "Crypt/RSA.php";
$publickey  = file_get_contents('../../budprivate/publickey.pem');
$rsa = new Crypt_RSA();

$new_registration = isset($_POST['newreg']) ? true : false;
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
if (!$email) {
    echo "bad";
    exit;
}

// text for message body
$msg = <<<LNK
<h3>Do not reply to this message</h3>
<p>Your request to reset your Budgetizer account was received<br />
Your User Name is:
LNK;
$newreg = <<<REG
<h3>Do not reply to this message</h3>
<p>You have a new account with MyBudgetizer.com for
User Name: 
REG;
$post = <<<PASS
</p><p>Click this link to setup your new account password,
select your security questions, and complete the registration
process:</p>
PASS;
$href = '<a href="https://mybudgetizer.com/accounts/renew.php?code=';

// security code:
$tmp_pass = bin2hex(random_bytes(5)); // 10 hex characters
$hash = password_hash($tmp_pass, PASSWORD_DEFAULT);
$registered
    = $pdo->query('SELECT email FROM `Users`;')->fetchAll(PDO::FETCH_COLUMN);
if (in_array($email, $registered)) {
    // get username
    $udatReq = "SELECT `uid`,`username` FROM `Users` WHERE `email` = :email;";
    $udat = $pdo->prepare($udatReq);
    $udat->execute(["email" => $email]);
    $user_data = $udat->fetch(PDO::FETCH_ASSOC);
    $uid = $user_data['uid'];
    $rsa->loadKey($publickey);
    $uname = $user_data['username'];
    $newcipher = hex2bin($uname);
    $orgname = $rsa->decrypt($newcipher);
    // save temporary secure password
    $savecodeReq = "UPDATE `Users` SET `password` = ? WHERE `uid` = ?;";
    $savecode = $pdo->prepare($savecodeReq);
    $savecode->execute([$hash, $uid]);
} else {
    echo "nofind";
}
if (!$new_registration) {   
    $msg .= ' ' . $orgname . '</p>';
    $msg .= '<p>A temporary password has been assigned: ';
    $msg .= '<strong>' . $tmp_pass . '</strong></p>';
    $href .= $tmp_pass . '&ix=' . $uid .
        '"><span style="font-size:16px;">Click to reset your password</span></a>';
    $msg .= $href;
    $user_msg = $msg;
    $subject = "Password Update";
} else { // new registration
    $newreg .= $orgname . '<br />Your temporary login code is ' . $tmp_pass;
    $newreg .= $post . $href . $tmp_pass . '&ix=' . $uid .
        '&reg=y">New Account Link</a>';
    $user_msg = $newreg;
    $subject = "New Account";
}
$mail->isHTML(true);
$mail->setFrom('webmaster@mybudgetizer.com', 'Do not reply');
$mail->addAddress($email, 'Budgetizer User');
$mail->Subject = $subject;
$mail->Body = $user_msg;
@$mail->send();
echo "ok";
