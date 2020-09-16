<?php
/**
 * If there are cookies on the client's browser for this site (epizy),
 * then they are used. If the cookie has expired, the user has the opportunity
 * to renew.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();

if (!isset($_SESSION['userid'])) {
    // Clear out any partial logins created during testing
    unset($_SESSION['cookiestatus']);
    unset($_SESSION['expire']);
    unset($_SESSION['cookies']);
    unset($_SESSION['start']);

    $cstat = "NOLOGIN"; // $cstat & $start are recorded on page for getLogin.js
    $start = '000';
    $regusr = isset($_COOKIE['epiz']) ? true : false; // registered user?
    if ($regusr) {
        $uname = $_COOKIE['epiz'];
        $userReq = "SELECT * FROM Users WHERE username = ?;";
        $user_dat = $pdo->prepare($userReq);
        $user_dat->execute([$uname]);
        $rowcnt = $user_dat->rowCount();
        if ($rowcnt === 0) {
            $cstat = 'NONE';
        } elseif ($rowcnt === 1) {
            $cstat = "OK";
            $fetched = $user_dat->fetch(PDO::FETCH_ASSOC);
            // now establish session login data
            $_SESSION['userid']       = $fetched['uid'];
            $_SESSION['expire']       = $fetched['passwd_expire'];
            $_SESSION['cookies']      = $fetched['cookies'];
            $_SESSION['start']        = $fetched['setup'];
            $start    = $fetched['setup'];
            $expDate  = $fetched['passwd_expire'];
            $american = str_replace("-", "/", $expDate);
            $orgDate  = strtotime($american);
            if ($orgDate <= time()) {
                $cstat = 'EXPIRED';
            } else {
                $UX_DAY = 60*60*24; // unix timestamp value for 1 day
                $days = floor(($orgDate - time())/$UX_DAY);
                if ($days <= 5) {
                    $cstat = 'RENEW';
                }
            }
            $_SESSION['cookiestatus'] = $cstat; // OK, EXPIRED, or RENEW
        } else {
            $cstat = 'MULTIPLE';
        }
    }
} else {
    // login session variables already exist
    $cstat = "OK";
    $start = $_SESSION['start'];
}
