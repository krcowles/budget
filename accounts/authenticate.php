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
chdir('../phpseclib1.0.20');
require "Crypt/RSA.php";
$rsa = new Crypt_RSA();
$publickey  = file_get_contents('../../budprivate/publickey.pem');
$rsa->loadKey($publickey);

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
$nomatch = true;
$user_dat = $pdo->query("SELECT * FROM `Users`")->fetchAll(PDO::FETCH_ASSOC);
foreach ($user_dat as $user) {
    if (strlen($user['username']) > 30) {
        $cipher = hex2bin($user['username']);
        $decrypted = $rsa->decrypt($cipher);
        $uid = $user['uid'];
        if ($decrypted === $username) {
            if (password_verify($userpass, $user['password'])) {
                $nomatch = false;
                $expiration = $user['passwd_expire'];
                $american = str_replace("-", "/", $expiration);
                $expdate = strtotime($american);
                if ($expdate <= time()) {
                    // remove user from Users table
                    $expiredUser = "DELETE FROM `Users` WHERE `uid`=?;";
                    $removeUser = $pdo->prepare($expiredUser);
                    $removeUser->execute([$user['uid']]);
                    $return_data['status'] = 'EXPIRED';
                    echo json_encode($return_data);
                    exit;
                } else {
                    $_SESSION['userid']  = $user['uid'];
                    // check for renewal status
                    $UX_DAY = 60*60*24; // unix timestamp value for 1 day
                    $days = floor(($expdate - time())/$UX_DAY);
                    if ($days <= 5) {
                        $return_data['status'] = 'RENEW';
                        echo json_encode($return_data);
                        exit;
                    }
                    // establish session, even if renewal is to take place
                    $_SESSION['cookies'] = $user['cookies'];
                    $_SESSION['start']   = $user['setup'];
                    if (count($entries) === 1) {
                        $pdo->query("DROP TABLE `Locks`;");
                    } else {
                        $dropLocks = $pdo->prepare(
                            "DELETE FROM `Locks` WHERE `ipaddr`=?;"
                        );
                        $dropLocks->execute([$ip]);
                    }
                }
                $return_data = array(
                    'status' => 'LOCATED',
                    'start' => $user['setup'],
                    'cookies' => $user['cookies'],
                    'ix' => $user['uid']
                ); 
            }
        }
    }
}
if ($nomatch) { // no user or bad password
    updateFailures(++$fails, $ip, $pdo);
    $return_data['status'] = 'FAIL';
    $return_data['fail_cnt'] = $fails;
}
echo json_encode($return_data);
