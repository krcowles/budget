<?php
/**
 * Log out of the budgetizer app; newuser is the admin's option
 * to switch to a new userid.
 * PHP Version 7.8
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license
 */
session_start();
require "../database/global_boot.php";

if (!isset($_GET['newuser'])) {
    setcookie('mybud', '', 0, '/');
    unset($_SESSION['userid']);
    unset($_SESSION['cookies']);
    unset($_SESSION['start']);
} else {
    $newid = filter_input(INPUT_GET, 'newuser');
    $_SESSION['userid'] = $newid;
}
