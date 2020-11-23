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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>The Budgetizer</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Rolling 3-month budget tracker" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <link href="styles/modals.css" type="text/css" rel="stylesheet" />
    <link href="index.css" type="text/css" rel="stylesheet" />
</head>

<body>

<div class="hcontainer">
    <div id="sitetext">
        <p id="maintext">The Budgetizer</p>
        <p id="subtext">A home budget <br />creation and management tool</p>
    </div>
    <img src="images/dollars.jpg" alt="dollar bills" />
</div>
<div id="logger">
    <div id="info" class="vcontainer">
        <div id="learn">
            <p  class="breaks">Beginner?<br />Learn <a
                href="help/help.php?doc=HowToBudget.pdf"
                target="_blank">How to Budget</a></p>
        </div>
        <div id="tools">
            <p class="breaks">See also, how to use<br /><a
                href="help/help.php?doc=Tools.pdf" target="_blank">
                Budgetizer Tools</a></p>
        </div>
    </div>
    <div id="login">
        <div id="banner">This site is totally free!</div>
        <div id="user">
            <form action="admin/secure_login.php" method="post">
            <table id="entry">
                <colgroup>
                    <col style="width:120px;">
                    <col style="width:200px;">
                </colgroup>
                <tbody>
                    <tr>
                        <td>Members:</td>
                        <td><input class="userinput" type="text" name="username"
                            placeholder="User Name" autocomplete="username" />
                        </td>
                    </tr>
                    <tr class="spacer">
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input class="userinput" type="password" name="password"
                            placeholder="Password" autocomplete="password" />
                        </td>
                    </tr>
                    <tr class="spacer">
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td id="button">
                            <button id="submit">Log In</button></td>
                    </tr>
                    <tr class="spacer">
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td id="forgot"><a id="resetpass" href="#">
                            Forgot Username or Password?</a></td>
                    </tr>
                </tbody>
            </table>
            </form>
        </div>
        <div id="register">
            <p>Or:&nbsp;&nbsp;<a href="admin/registration.html">
                sign up and start your new budget!</a></p>
        </div>
    </div>
</div>
<div>
    <p id="cookiestatus" style="display:none;"><?=$cstat?></p>
    <p id="startpg" style="display:none;"><?=$start;?></p>
</div>
<div id="usr_modal">
    <span id="enteremail">Enter your email address:</span>
    <input id="umail" type="text" name="umail"
        autocomplete="email" /><br /><br />
    <span id="mailtxt">Click to reset your login:</span><br />
    <button id="sendmail">Send</button>
</div>

<script src="scripts/jquery-1.12.1.js"></script>
<script src="scripts/getLogin.js"></script>
<script src="scripts/modals.js"></script>
<script src="index.js"></script>

</body>
</html>