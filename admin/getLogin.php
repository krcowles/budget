<?php
/**
 * If there are cookies on the client's browser for this site (mochahost),
 * then they are used. If the cookie has expired, the user has the opportunity
 * to renew.
 * PHP Version 7.8
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();

// check for partial logins, e.g. when expired password
if (!isset($_SESSION['userid']) || !isset($_SESSION['cookiestatus'])
    || !isset($_SESSION['expire']) || !isset($_SESSION['cookies']) 
    || !isset($_SESSION['start'])
) {
    unset($_SESSION['userid']);
    unset($_SESSION['cookiestatus']);
    unset($_SESSION['expire']);
    unset($_SESSION['cookies']);
    unset($_SESSION['start']);
}
if (!isset($_SESSION['userid'])) {
    $cstat = "NOLOGIN"; // $cstat & $start are recorded on page for getLogin.js
    $start = '000';
    $regusr = isset($_COOKIE['mybud']) ? true : false; // registered user?
    if ($regusr) {
        $uname = $_COOKIE['mybud'];
        $userReq = "SELECT * FROM Users WHERE username = ?;";
        $user_dat = $pdo->prepare($userReq);
        $user_dat->execute([$uname]);
        $rowcnt = $user_dat->rowCount();
        if ($rowcnt === 0) {
            $cstat = 'NONE';
        } elseif ($rowcnt === 1) {
            $cstat = "OK";
            $fetched = $user_dat->fetch(PDO::FETCH_ASSOC);
            $start    = $fetched['setup'];
            $expDate  = $fetched['passwd_expire'];
            $american = str_replace("-", "/", $expDate);
            $orgDate  = strtotime($american);
            if ($orgDate <= time()) {
                $cstat = 'EXPIRED';
                // no login credentials
            } else {
                // establish login credentials
                $_SESSION['userid']       = $fetched['uid'];
                $_SESSION['expire']       = $fetched['passwd_expire'];
                $_SESSION['cookies']      = $fetched['cookies'];
                $_SESSION['start']        = $fetched['setup'];
                $UX_DAY = 60*60*24; // unix timestamp value for 1 day
                $days = floor(($orgDate - time())/$UX_DAY);
                if ($days <= 5) {
                    $cstat = 'RENEW';
                }
            }
            if ($cstat !== 'EXPIRED') {
                $_SESSION['cookiestatus'] = $cstat; // OK or RENEW
            }
        } else {
            $cstat = 'MULTIPLE';
        }
    }
} else {
    // login session variables already exist
    $cstat = "OK";
    $start = $_SESSION['start'];
}
