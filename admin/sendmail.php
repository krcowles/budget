<?php
/**
 * This script uses the email address provided by the user and verifies its
 * existence in the 'Users' table. If present, an email is sent to the user
 * providing the user the account User Name and a temporary password to reset
 * the account.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../database/global_boot.php";
require "gmail.php";

// text for message body
$msg = <<<LNK
<h3>Do not reply to this message</h3>
Your request to reset your Budgetizer account was received<br />
Your User Name is:
LNK;
$href = '<br /><br /><a href="https://mybudgetizer.com/admin/renew.php?code=';
// validate email
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
if (!$email) {
    echo "bad";
    exit;
}

$registered
    = $pdo->query('SELECT email FROM `Users`;')->fetchAll(PDO::FETCH_COLUMN);
if (in_array($email, $registered)) {
    // get username
    $uname = "SELECT `username` FROM `Users` WHERE `email` = :email;";
    $stmnt = $pdo->prepare($uname);
    $stmnt->execute(["email" => $email]);
    $user = $stmnt->fetch(PDO::FETCH_ASSOC);
    $msg .= ' ' . $user['username'] . '<br />';
    // create temporary password
    $tmp_pass = bin2hex(random_bytes(5)); // 10 hex characters
    $hash = password_hash($tmp_pass, PASSWORD_DEFAULT);
    $savecodeReq = "UPDATE `Users` SET `password` = ? WHERE `username` = ?;";
    $savecode = $pdo->prepare($savecodeReq);
    $savecode->execute([$hash, $user['username']]);
    $msg .= 'A temporary password has been assigned: ';
    $msg .= '<strong>' . $tmp_pass . '</strong>';
    $href .= $tmp_pass . 
        '"><span style="font-size:16px;">Click to activate</span></a>';
    $msg .= $href;
    // send it
    $mail->isHTML(true);
    $mail->setFrom('webmaster@mybudgetizer.com', 'Do not reply');
    $mail->addAddress($email, 'Budgetizer User');
    $mail->Subject = 'Account Reset';
    $mail->Body = $msg;
    @$mail->send();
    echo "ok";
} else {
    echo "nofind";
}
