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
$month_digits = $today['mon'];
$month_string = $today['month'];
$day = $today['mday'];
$year = intval($today['year']);
$year++;
$exp_date = $year . "-" . $month_digits . "-" . $day;
if ($submitter == 'create') {
    $email  = isset($_POST['email']) ? 
        filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) : false;
    $newuser = "INSERT INTO `Users` (`email`,`username`,`LCM`,`password`," .
        "`passwd_expire`) " .
        "VALUES (:email,:uname,'{$month_string}',:passwd,:pass_exp);";
    $user = $pdo->prepare($newuser);
    $user->execute(
        array( ":email" => $email, ":uname" =>  $username, ":passwd" => $password,
        ":pass_exp" => $exp_date)
    );
} else { // update user
    $updateuser = "UPDATE Users SET `password`=?,`passwd_expire`=? " .
        "WHERE username=?;";
    $update = $pdo->prepare($updateuser);
    $update->execute(
        array($password, $exp_date, $username)
    );
}
// always try to set a user cookie:
$days = 365; // Number of days before cookie expires
$expire = time()+60*60*24*$days;
setcookie("epiz", $username, $expire, "/");
echo "DONE";
