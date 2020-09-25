<?php
/**
 * This script will update the Users table with the form information 
 * entered by the new user on Registration.html, or update for renewal.
 * If the user 'Forgot Password', the session data will also be set.
 * PHP Version 7.1
 * 
 * @package Admin
 * @author  Tom Sandberg and Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require_once "../database/global_boot.php";

$submitter = filter_input(INPUT_POST, 'submitter');
$username  = filter_input(INPUT_POST, 'username');
$user_pass = filter_input(INPUT_POST, 'password');
$choice    = filter_input(INPUT_POST, 'cookies');
$_SESSION['cookies'] = $choice;

$password  = password_hash($user_pass, PASSWORD_DEFAULT);
$today = getdate();
$month_digits = $today['mon'];
$month_string = $today['month'];
$day = $today['mday'];
$year = intval($today['year']);
$year++;
$exp_date = $year . "-" . $month_digits . "-" . $day;
if ($submitter == 'create') {
    $email  = isset($_POST['email']) ? 
        filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) : false;
    $newuser = "INSERT INTO `Users` (`email`,`username`,`setup`," .
        "`LCM`,`password`,`passwd_expire`,`cookies`) " .
        "VALUES (:email,:uname,'000','{$month_string}'," .
        ":passwd,:pass_exp,:cookies);";
    $user = $pdo->prepare($newuser);
    $user->execute(
        array( ":email" => $email, ":uname" =>  $username, ":passwd" => $password,
        ":pass_exp" => $exp_date, ":cookies" => $choice)
    );
    $last = $pdo->query("SELECT * FROM `Users` ORDER BY 1 DESC LIMIT 1;");
    $newid = $last->fetch(PDO::FETCH_ASSOC);
    $_SESSION['userid']       = $newid['uid']; // already incremented via INSERT
    $_SESSION['expire']       = $exp_date;
    $_SESSION['cookiestatus'] = "OK";
    $_SESSION['start']        = '000';
} else { // renew: update user
    if ($username !== 'notmail') {
        // if changing password via email, there are no login credentials yet
        $getUserReq = "SELECT * FROM `Users` WHERE `username` = :uname;";
        $getUser = $pdo->prepare($getUserReq);
        $getUser->execute(["uname" => $username]);
        $user_dat = $getUser->fetch(PDO::FETCH_ASSOC);
        $_SESSION['userid'] = $user_dat['uid'];
        $_SESSION['expire'] = $user_dat['passwd_expire'];
        $_SESSION['cookiestatus'] = "OK";
        $_SESSION['start'] = $user_dat['setup'];
        $_SESSION['cookies'] = $choice;
    }
    $updateuser = "UPDATE `Users` SET `password`=?, `passwd_expire`=?, " .
        "`cookies`=? WHERE `uid`=?;";
    $update = $pdo->prepare($updateuser);
    $update->execute(
        array($password, $exp_date, $choice, $_SESSION['userid'])
    );
}
if ($_SESSION['cookies'] === 'accept') {
    $days = 365; // Number of days before cookie expires
    $expire = time()+60*60*24*$days;
    setcookie("epiz", $username, $expire, "/", "", false, true);
}
echo "DONE";
