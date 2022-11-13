<?php
/**
 * This script is invoked when a user an ajax error was encountered
 * in production mode. The admin is notified of the error and its code.
 * Because of the number of ajax calls, the message construction has
 * many optios.
 * PHP Version 7.4
 * 
 * @package MedRefs
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require "../../database/global_boot.php";
require "../accounts/gmail.php";
verifyAccess('ajax');

$errmsg = filter_input(INPUT_POST, 'err'); // always present
$username = isset($_SESSION['userid']) ? $_SESSION['userid'] : 'no user';

$admin_msg = "User " . $username . " encountered an ajax error: " . 
    PHP_EOL . $errmsg . PHP_EOL;
$to = ADMIN_EMAIL;
$subject = "User ajax error";

$mail->isHTML(true);
$mail->setFrom('webmaster@medrefs.com', 'Do not reply');
$mail->addAddress($to, 'MedRefs Admin');
$mail->Subject = $subject;
$mail->Body = $admin_msg;
@$mail->send();