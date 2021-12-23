<?php
/**
 * This script authenticates the username/password combo entered by
 * the user when submitting a login request. The information is 
 * compared to entries in the Users table. The successful login always
 * attempts to set a cookie for this user. This script is invoked via
 * ajax from getLogin.js -> validateUser(). Multiple failed attempts
 * results in user login blockout for 1 hour, or via email link available
 * to the user when the lockout occurs.
 * PHP Version 7.4
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require "../database/global_boot.php";

$username = filter_input(INPUT_POST, 'usr_name');
$userpass = filter_input(INPUT_POST, 'usr_pass');
// Track number of failed attempts
if (!isset($_SESSION['fails'])) {
    $_SESSION['fails'] = 0;
    $_SESSION['ftime'] = time(); // time re-established when 3 failures occur
} else {
    $lapsed = time() - $_SESSION['ftime'];
    if ($lapsed > 60*30) {
        unset($_SESSION['fails']);
        unset($_SESSION['ftime']);
    } elseif ($_SESSION['fails'] >= 3) {
        echo "FAIL&" . $_SESSION['fails'];
    }
}

$usr_req = "SELECT * FROM `Users` WHERE BINARY `username` = :usr;";
$auth = $pdo->prepare($usr_req);
$auth->bindValue(":usr", $username);
$auth->execute();
$rowcnt = $auth->rowCount();
if ($rowcnt === 1) {  // located single instance of user
    $user_dat = $auth->fetch(PDO::FETCH_ASSOC);
    if (password_verify($userpass, $user_dat['password'])) {
        $_SESSION['userid'] = $user_dat['uid'];
        $expiration = $user_dat['passwd_expire'];
        $american = str_replace("-", "/", $expiration);
        $expdate = strtotime($american);
        if ($expdate <= time()) {
            // for renewal, need only userid and cookies status
            $_SESSION['cookiestatus'] = 'EXPIRED';
            echo "EXPIRED";
            exit;
        } else {
            // establish remaining login credentials
            $_SESSION['expire'] = $expiration;
            $_SESSION['cookies'] = $user_dat['cookies'];
            $_SESSION['cookiestatus'] = "OK";
            $_SESSION['start'] = $user_dat['setup'];
            $UX_DAY = 60*60*24; // unix timestamp value for 1 day
            $days = floor(($expdate - time())/$UX_DAY);
            if ($days <= 5) {
                $_SESSION['cookiestatus'] = 'RENEW';
                echo "RENEW";
                exit;
            }
        }
        echo "LOCATED&" . $_SESSION['start'] . "&" . $_SESSION['cookies'];
    } else {  // user exists, but password doesn't match:
        $_SESSION['fails']++;
        if ($_SESSION['fails'] === 3) {
            $_SESSION['ftime'] = time();
        }
        echo "FAIL&" . $_SESSION['fails'];
    }
} else {  // either bad username [rowcnt = 0], or multiple entries (shouldn't happen)
    $_SESSION['fails']++;
    if ($_SESSION['fails'] === 3) {
        $_SESSION['ftime'] = time();
    }
    echo "FAIL&" . $_SESSION['fails'];
}
