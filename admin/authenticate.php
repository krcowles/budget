<?php
/**
 * This script authenticates the username/password combo entered by
 * the user when submitting a login requiest. The information is 
 * compared to entries in the Users table. The successful login always
 * attempts to set a cookie for this user. This script is invoked via ajax.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../database/global_boot.php";

define("UX_DAY", 60*60*24); // unix timestamp value for 1 day
$usrname = filter_input(INPUT_POST, 'usr_name');
$usrpass = filter_input(INPUT_POST, 'usr_pass');
$usr_req = "SELECT username,`password`,passwd_expire FROM Users WHERE username = :usr;";
$auth = $pdo->prepare($usr_req);
$auth->bindValue(":usr", $usrname);
$auth->execute();
$rowcnt = $auth->rowCount();
if ($rowcnt === 1) {  // located single instance of user
    $user_dat = $auth->fetch(PDO::FETCH_ASSOC);
    if (password_verify($usrpass, $user_dat['password'])) {  // user data correct
        $expiration = $user_dat['passwd_expire'];
        $american = str_replace("-", "/", $expiration);
        $expdate = strtotime($american);
        if ($expdate <= time()) {
            echo "EXPIRED";
            exit;
        } else {
            $days = floor(($expdate - time())/UX_DAY);
            if ($days <= 5) {
                // set current cookie pending renewal
                setcookie('epiz', $usrname, $expdate, "/");
                echo "RENEW";
                exit;
            }
        }
        setcookie('epiz', $usrname, $expdate, "/");
        echo "LOCATED";
    } else {  // user exists, but password doesn't match:
        echo "BADPASSWD" . $usrpass . ";" . $user_dat['password'];
    }
} else {  // not in User table (or multiple entries for same user)
    echo "FAIL";
}
