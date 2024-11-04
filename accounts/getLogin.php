<?php
/**
 * If there are cookies on the client's browser for this server site,
 * then they are used. The index page records the status of the cookie,
 * if found: OK, EXPIRED, RENEW. If not found: NONE, MULTIPLE. No cookie: NOLOGIN
 * PHP Version 7.4
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
// check for no previous or 'partial' logins
if (!isset($_SESSION['userid']) || !isset($_SESSION['cookies'])
    || !isset($_SESSION['start'])
) {
    unset($_SESSION['userid']);
    unset($_SESSION['cookies']);
    unset($_SESSION['start']);
}
if (!isset($_SESSION['userid'])) {
    // $cstat & $start are recorded on index page for getLogin.js
    $cstat = "NOLOGIN";
    $start = '000';
    $regusr = isset($_COOKIE['mybud']) ? true : false; // site cookie?
    if ($regusr) {
        // login status when cookies are accepted; no cookies => NOLOGIN
        $uname = $_COOKIE['mybud'];
        $userReq = "SELECT * FROM Users WHERE username = ?;";
        $user_dat = $pdo->prepare($userReq);
        $user_dat->execute([$uname]);
        $rowcnt = $user_dat->rowCount();
        if ($rowcnt === 0) {
            $cstat = 'NONE'; // no user found with this cookie's username
        } elseif ($rowcnt === 1) {
            $cstat = "OK";
            $fetched = $user_dat->fetch(PDO::FETCH_ASSOC);
            $start    = $fetched['setup'];
            $expDate  = $fetched['passwd_expire'];
            $american = str_replace("-", "/", $expDate);
            $orgDate  = strtotime($american);
            // can used by renew.php for either expired or renew
            $_SESSION['userid']  = $fetched['uid'];
            if ($orgDate <= time()) {
                $cstat = 'EXPIRED';
                // remove user from Users table (no session yet exists)
                $expiredUser = "DELETE FROM `Users` WHERE `uid`=?;";
                $removeUser = $pdo->prepare($expiredUser);
                $removeUser->execute([$_SESSION['userid']]);
                // kill any cookies for this user
                setcookie('mybud', '', 0, '/');
            } else {
                // establish remaining login credentials
                $_SESSION['cookies'] = $fetched['cookies'];
                $_SESSION['start']   = $fetched['setup'];
                $UX_DAY = 60*60*24; // unix timestamp value for 1 day
                $days = floor(($orgDate - time())/$UX_DAY);
                if ($days <= 5) {
                    $cstat = 'RENEW';
                } else {
                    header("Location: main/displayBudget.php");
                }
            }
        } else {
            $cstat = 'MULTIPLE'; // multiple entries for this username
        }
    }
} else {
    // login session variables already exist
    $cstat = "OK";
    $start = $_SESSION['start'];
    header("Location: main/displayBudget.php");
}
