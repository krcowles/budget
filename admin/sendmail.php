<?php
/**
 * This script uses the email address provided by the user and verifies its
 * existence in the 'Users' table. If present, an email is sent to the user
 * provding the user his/her login name.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../database/global_boot.php";
require "gmail.php";

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
if (!$email) {
    echo "bad";
    exit;
}

$registered = $pdo->query('SELECT email FROM `Users`;')->fetchAll(PDO::FETCH_COLUMN);
if (in_array($email, $registered)) {
    $uname = "SELECT `username` FROM `Users` WHERE `email` = :email;";
    $stmnt = $pdo->prepare($uname);
    $stmnt->execute(["email" => $email]);
    $user = $stmnt->fetch(PDO::FETCH_ASSOC);
    $mail->setFrom('webmaster@budgetizer.epizy.com', 'Do not reply');
    $mail->addAddress($email, 'Budgetizer User');
    $mail->Subject = 'Your Budgetizer User Name';
    $mail->Body = "Your Budgetizer User Name is " . $user['username'];
    // ... or send an email with HTML.
    //$mail->msgHTML(file_get_contents('contents.html'));
    // Optional when using HTML: Set an alternative plain text message 
    //                                   for email clients who prefer that.
    //$mail->AltBody = 'This is a plain-text message body'; 
    // Optional: attach a file
    //$mail->addAttachment('images/phpmailer_mini.png');
    @$mail->send();
    echo "ok";
    /*
    if ($mail->send()) {
        echo "ok";
        exit;
    } else {
        $msg = "Error: " . $mail->ErrorInfo;
        exit;
    }
    */
} else {
    echo "nofind";
}
