<?php
/**
 * Log out of the budgetizer app
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license
 */
session_start();

setcookie('epiz', '', 0, '/');
unset($_SESSION['userid']);
unset($_SESSION['expire']);
unset($_SESSION['cookiestatus']);
unset($_SESSION['cookies']);
unset($_SESSION['start']);
