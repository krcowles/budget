<?php
/**
 * This script allows the user to submit information pertinent to becoming
 * a registered user, which allows him/her to create new pages and to edit
 * those pages. 
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Tom Sandberg and Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require_once "../database/global_boot.php";
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
    <title>New User Registration</title>
    <meta charset="utf-8" />
    <meta name="description" content="New user sign-up" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <link href="../styles/standards.css" type="text/css" rel="stylesheet" />
    <link href="../styles/jquery-ui.css" type="text/css" rel="stylesheet" />
    <link href="../styles/registration.css" type="text/css" rel="stylesheet" />
    <script src="../scripts/jquery-1.12.1.js"></script>
    <script src="../scripts/jquery-ui.js"></script>
    <script src="../scripts/jquery.validate.min.js"></script>
    <script src="../scripts/jquery.validate.password.js"></script>
</head>

<body>
<div id="container">
<h2 class="NormalHeading">
    Welcome to the Budgetizer Website!
</h2>
<p class="SmallHeading">Please fill out the form below to become a (free) 
    registered user. Only the minimum information is required to secure your login.
</p>
<form id="form" method="POST" action="#">
<fieldset>
    <legend>Required Information</legend>
    <label for="email">Your email address (only used if you forget your 
        password)</label>
    <input type="text" name="email" size="40" class="email" 
            maxlength="60" value="" /><br />
    <label for="username">Your user name for logging in (30 characters max)</label>
    <input type="text" name="username" size="40" 
            maxlength="30" value="" /><br />
    <p>Note: Passwords must be at least 8 characters long and 
        should contain a mix of characters (alpha, numeric, special).</p>
    <label for="password">Enter a password: </label>
    <input id="passwd" type="password" name="password" size="20"
            class="password" />&nbsp;
    <div class="password-meter">
        <div class="password-meter-message"></div>
        <div class="password-meter-bg">
            <div class="password-meter-bar"></div>
        </div>
        <br />
        <div id ="confirm">
        <label for="confirm_password">Confirm password: </label>
        <input id="confirm_password" type="password" 
                name="confirm_password" size="20" class="required" />
        </div>
    </div><br />
</fieldset><br /><br />
<button id="formsubmit">Submit My Info</button>
</form>
</div>   <!-- end of container -->

<script src="../scripts/registration.js" type="text/javascript"></script>

</body>
</html>
