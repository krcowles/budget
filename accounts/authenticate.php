<?php
/**
 * This script authenticates the username/password combo entered by the
 * the user when submitting a login request. The information is compared
 * to entries in the Users table. This script is invoked via ajax from
 * getLogin.js -> validateUser(). Multiple failed attempts results in login
 * lockout for 1 hour, or via email link available to the user when the
 * lockout occurs. Note that the user's IP address is logged to identify
 * cases where user attempts to use a different browser to bypass the lockout.
 * As with getLogin.php, a successful login (even when 'RENEW') establishes
 * the session variables.
 * PHP Version 7.4
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require "../database/global_boot.php";
require "../accounts/accountFunctions.php";

$username = filter_input(INPUT_POST, 'usr_name');
$userpass = filter_input(INPUT_POST, 'usr_pass');

$ip = getIpAddr();
$ip = $ip === "::1" ? '127.0.0.1' : $ip; // Chrome localhost ipaddr is different
$fails = 0;

$iptbl = <<<TBL
CREATE TABLE IF NOT EXISTS `Locks` (
    `indx`   smallint NOT NULL AUTO_INCREMENT,
    `ipaddr` varchar(15) DEFAULT NULL,
    `fails`  smallint DEFAULT 0,
    `lockout` datetime DEFAULT NULL,
    PRIMARY KEY (`indx`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
TBL;
$pdo->query($iptbl);

// Set '$fails' to be the current value in the db (or default 0)
$entryReq = "SELECT `ipaddr`,`fails` FROM `Locks`;";
$entries = $pdo->query($entryReq)->fetchAll(PDO::FETCH_KEY_PAIR);
if (count($entries) === 0) {
    $entry = "INSERT INTO `Locks` (`ipaddr`,`fails`) VALUES (?, 0);";
    $adduser = $pdo->prepare($entry);
    $adduser->execute([$ip]);
} else {
    if (array_key_exists($ip, $entries)) {
        $fails = $entries[$ip];
    } else {
        $entry = "INSERT INTO `Locks` (`ipaddr`,`fails`) VALUES (?, 0);";
        $adduser = $pdo->prepare($entry);
        $adduser->execute([$ip]);
    }
}
// validate user info (prior to security questions)
$usr_req = "SELECT * FROM `Users` WHERE BINARY `username` = :usr;";
$auth = $pdo->prepare($usr_req);
$auth->bindValue(":usr", $username);
$auth->execute();
$rowcnt = $auth->rowCount();
if ($rowcnt === 1) {  // located single instance of user
    $user_dat = $auth->fetch(PDO::FETCH_ASSOC);
    if (password_verify($userpass, $user_dat['password'])) {
        $return_data = [];
        $expiration = $user_dat['passwd_expire'];
        $american = str_replace("-", "/", $expiration);
        $expdate = strtotime($american);
        if ($expdate <= time()) {
            // remove user from Users table
            $expiredUser = "DELETE FROM `Users` WHERE `uid`=?;";
            $removeUser = $pdo->prepare($expiredUser);
            $removeUser->execute([$user_dat['uid']]);
            $return_data['status'] = 'EXPIRED';
            echo json_encode($return_data);
            exit;
        } else {
            $_SESSION['userid']  = $user_dat['uid'];
            // check for renewal status
            $UX_DAY = 60*60*24; // unix timestamp value for 1 day
            $days = floor(($expdate - time())/$UX_DAY);
            if ($days <= 5) {
                $return_data['status'] = 'RENEW';
                echo json_encode($return_data);
                exit;
            }
            // establish session, even if renewal is to take place
            $_SESSION['cookies'] = $user_dat['cookies'];
            $_SESSION['start']   = $user_dat['setup'];
            if (count($entries) === 1) {
                $pdo->query("DROP TABLE `Locks`;");
            } else {
                $dropLocks = $pdo->prepare("DELETE FROM `Locks` WHERE `ipaddr`=?;");
                $dropLocks->execute([$ip]);
            }
        }
        $return_data = array('status' => 'LOCATED', 'start' => $user_dat['setup'],
            'cookies' => $user_dat['cookies'], 'ix' => $user_dat['uid']); 
    } else {  // user exists, but password doesn't match:
        updateFailures(++$fails, $ip, $pdo);
        $return_data['status'] = 'FAIL';
        $return_data['fail_cnt'] = $fails;
    }
} else {  // either bad username [rowcnt = 0], or multiple entries (shouldn't happen)
    updateFailures(++$fails, $ip, $pdo);
    $return_data['status'] = 'FAIL';
    $return_data['fail_cnt'] = $fails;
}
echo json_encode($return_data);
