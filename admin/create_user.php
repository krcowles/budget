<?php
/**
 * This script will update the Users table with the form information 
 * entered by the new user on Registration.html, or update for renewal.
 * PHP Version 7.1
 * 
 * @package Admin
 * @author  Tom Sandberg and Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require_once "../database/global_boot.php";
$submitter = filter_input(INPUT_POST, 'submitter');
$username  = filter_input(INPUT_POST, 'username');
$user_pass = filter_input(INPUT_POST, 'password');
$password  = password_hash($user_pass, PASSWORD_DEFAULT);
$today = getdate();
$month = $today['mon'];
$day = $today['mday'];
$year = intval($today['year']);
$year++;
$exp_date = $year . "-" . $month . "-" . $day;
$email     = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
if (!$email) {
    echo "Invalid email address - please go back to the Registration Page";
} 
if ($submitter == 'create') {
    $newuser = "INSERT INTO Users (" .
    "email,username,password,passwd_expire) " .
        "VALUES (:email,:uname,:passwd,:pass_exp);";
    $user = $pdo->prepare($newuser);
    $user->execute(
        array( ":email" => $email, ":uname" =>  $username, ":passwd" => $password,
        ":pass_exp" => $exp_date)
    );
} else { // update user
    $updateuser = "UPDATE Users SET `password`=?, " .
        "`passwd_expire`=? WHERE username=?;";
    $update = $pdo->prepare($updateuser);
    $update->execute(
        array($password, $exp_date, $username)
    );
}
// always try to set a user cookie:
$days = 365; // Number of days before cookie expires
$expire = time()+60*60*24*$days;
setcookie("epiz", $username, $expire, "/");
if ($submitter == 'create') {
    echo "DONE";
} else {
    $newbud = "../edit/newBudget.php?new=y&user=" . $username;
    header("Location: {$newbud}");
}
