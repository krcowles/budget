<?php
/**
 * This is a simple script to log out the curent user by unsetting
 * the user's cookies and the session variables associated with login.
 * Note: when an expired user (cookie or login) has been detected,
 * the user is removed from the USERS table.
 * PHP Version 7.4
 * 
 * @package MedRefs
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require "../php/global_boot.php";

if (isset($_GET['expire']) && $_GET['expire'] === 'Y') {
    $removeReq = "DELETE FROM `Users` WHERE `userid`=?;";
    $remove = $pdo->prepare($removeReq);
    $remove->execute([$_SESSION['userid']]);
}
if (isset($_SESSION['userid'])) {  // since session may have expired
    setcookie(SITE_REF, '', 0, '/');
    $admin = true;
}
unset($_SESSION['userid']);
unset($_SESSION['cookies']);
unset($_SESSION['cookie_state']);
unset($_SESSION[SITE_REF]);