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
define("UX_DAY", 60*60*24); // unix timestamp value for 1 day

$regusr = isset($_COOKIE['epiz'])   ? true : false; // registered user?
$uname = 'none';
$cstat = 'OK'; // changed below based on user cookie expiration data
$start = '';
if ($regusr) { // if no cookie, $uname remains 'none'
    $uname = $_COOKIE['epiz'];
    $expirationReq = "SELECT passwd_expire, setup FROM Users WHERE username = ?;";
    $userExpire = $pdo->prepare($expirationReq);
    $userExpire->execute([$uname]);
    $rowcnt = $userExpire->rowCount();
    if ($rowcnt === 0) {
        $cstat = 'NONE';
    } elseif ($rowcnt === 1) {
        $fetched = $userExpire->fetch(PDO::FETCH_ASSOC);
        $expDate = $fetched['passwd_expire'];
        $start = $fetched['setup'];
        $american = str_replace("-", "/", $expDate);
        $orgDate = strtotime($american);
        if ($orgDate <= time()) {
            $cstat = 'EXPIRED';
        } else {
            $days = floor(($orgDate - time())/UX_DAY);
            if ($days <= 5) {
                $cstat = 'RENEW';
            }
        }
    } else {
        $cstat = 'MULTIPLE';
    }
}
