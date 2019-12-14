<?php
/**
 * This page simply allows a user to login and proceed to the budget tracking
 * program. If user cookies are enabled and previous registration has occurred,
 * this page will be by-passed and the user redirected to main/budget.php.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require_once "database/global_boot.php";
require "admin/getLogin.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Welcome to Budget Tracking</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Rolling 4-month budget tracker" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <link href="styles/index.css" type="text/css" rel="stylesheet" />
    <link href="styles/modals.css" type="text/css" rel="stylesheet" />
</head>

<body>
<div id="welcome">
    <img src="images/BudgetHome.jpg" alt="Home Page for Site: Flowers" />
</div>
<div id="login">
    <div id="intro">
        <em>Welcome to Budgetizer</em><br />
        <span id="subtext">A budget creation & management tool</span>
    </div>
    <div id="userbox">
        <em><span id="free">This site is totally free!</span></em><br />
        Login: &nbsp;<input id="user" type="text" name="user" /><br />
        No login?&nbsp;&nbsp;&nbsp;<a id="signup" href="admin/registration.php" 
            target="_self">Sign me up!</a>
    </div>
    <div id="log_modal">
        <form id="passform" method="POST" action="#">
            <span id="modpass">Password: </span>
            <input id="passin" type="password" name="passwd" />
            <input id="moduser" type="hidden" name="user" value="" /><br />
        </form>
        <span id="rp">Forget Password? <a id="redopass" href="admin/renew.php"
            target="_blank">Reset Password</a></span>
    </div>
</div>
<div>
    <p id="usrcookie" style="display:none"><?= $uname;?></p>
    <p id="cookiestatus" style="display:none"><?= $cstat?></p>
</div>

<script src="scripts/jquery-1.12.1.js" type="text/javascript"></script>
<script src="scripts/modals.js" type="text/javascript"></script>
<script src="scripts/getLogin.js" type="text/javascript"></script>

</body>
</html>
