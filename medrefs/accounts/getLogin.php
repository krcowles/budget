<?php
/**
 * If there are cookies on the client's browser for this server site,
 * then they are used. The index.php page records the status of cookie
 * testing. If found: OK, EXPIRED, RENEW; if found but database has no
 * corresponding username, or multiple instances of the username exist:
 * NONE, MULTIPLE. If no cookie is found: NOLOGIN
 * NOTE that, since multiple designs are underway locally, other sessions
 * may exist with the same session variable names, so a variable for this
 * site (medref) is also established to verify access to the this site.
 * PHP Version 7.4
 * 
 * @package MedRefs
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();

/**
 * Complete set of session variables:
 * $_SESSION['userid']
 * $_SESSION['cookies']
 * $_SESSION['cookie_state']
 * $_SESSION[SITE_REF]
 */
// check for 'partial' logins
if (!isset($_SESSION['userid']) || !isset($_SESSION['cookies'])
    || !isset($_SESSION['cookie_state']) || !isset($_SESSION[SITE_REF])
) {
    unset($_SESSION['userid']);
    unset($_SESSION['cookies']);
    unset($_SESSION['cookie_state']);
    unset($_SESSION[SITE_REF]);
}
$admin = false;
if (!isset($_SESSION['userid'])) { // if not set, no session is active
    $cstat = "NOLOGIN";
    // $cstat is recorded on index page for access by javascript
    $regusr = isset($_COOKIE[SITE_REF]) ? true : false;
    // no cookies => NOLOGIN
    if ($regusr) {
        $uname = $_COOKIE[SITE_REF];
        $userReq = "SELECT * FROM Users WHERE username = ?;";
        $user_dat = $pdo->prepare($userReq);
        $user_dat->execute([$uname]);
        $rowcnt = $user_dat->rowCount();
        $_SESSION[SITE_REF] = SITE_REF;
        if ($rowcnt === 0) {
            $cstat = 'NONE'; // no user found with this cookie's username
            $_SESSION['userid'] = "";
            $_SESSION['cookies'] = "";
        } elseif ($rowcnt === 1) {
            $cstat = "OK";
            $fetched = $user_dat->fetch(PDO::FETCH_ASSOC);
            if ($fetched['userid'] === 1) {
                $admin = true;
            }
            $expDate  = $fetched['passwd_expire'];
            $american = str_replace("-", "/", $expDate);
            $orgDate  = strtotime($american);
            // can used by renew.php for either expired or renew
            $_SESSION['userid'] = $fetched['userid'];
            if ($orgDate <= time()) {
                $cstat = 'EXPIRED';
                // remove user from Users table (no session yet exists)
                $expiredUser = "DELETE FROM `Users` WHERE `userid`=?;";
                $removeUser = $pdo->prepare($expiredUser);
                $removeUser->execute([$_SESSION['userid']]);
                // kill any cookies for this user
                setcookie(SITE_REF, '', 0, '/');
            } else {
                // establish remaining login credentials
                $_SESSION['cookies'] = $fetched['cookies'];
                $UX_DAY = 60*60*24; // unix timestamp value for 1 day
                $days = floor(($orgDate - time())/$UX_DAY);
                if ($days <= 5) {
                    $cstat = 'RENEW';
                }
            }
        } else {
            $cstat = 'MULTIPLE'; // multiple entries for this username
            $_SESSION['cookies'] = "";
            $_SESSION['userid'] = "";
        }
    } else { // $cstat is already set to "NOLOGIN"
        $_SESSION['cookies'] = "";
        $_SESSION['userid'] = "";
    }
    $_SESSION['cookie_state'] = $cstat;
} else {  // login session variables already exist
    // for admins, assume userid = 1
    if ($_SESSION['userid'] === 1) {
        $admin = true;
    }
    $cstat = "OK";
}
