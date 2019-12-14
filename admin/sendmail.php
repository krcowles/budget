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
    $msg = "Your Budgetizer User Name is " . $user['username'];
    mail($mail, 'Budgetizer User Name', $msg);
    echo "ok";
} else {
    echo "nofind";
}
