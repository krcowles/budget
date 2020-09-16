<?php
/**
 * This script will allow the user to renew his/her password if he/she
 * has opted to do so.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require "../database/global_boot.php";
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
    <title>Profile/Password Renewal</title>
    <meta charset="utf-8" />
    <meta name="description" content="User update password et al" />
    <meta name="author" content="Tom Sandberg and Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <link href="../styles/jquery-ui.css" type="text/css" rel="stylesheet" />
    <link href="../styles/standards.css" type="text/css" rel="stylesheet" />
    <link href="../styles/registration.css" type="text/css" rel="stylesheet" />
    <style type="text/css">
        body { margin: 0px;}
        #formsubmit {
            width: 230px;
            height: 28px;
            font-size: 18px;
            color: brown;
            margin-bottom: 18px;
        }
        #formsubmit:hover {
            cursor: pointer;
            background-color: honeydew;
            font-weight: bold;
        }
    </style>
    <script src="../scripts/jquery-1.12.1.js"></script>
    <script src="../scripts/jquery-ui.js"></script>
    <script src="../scripts/jquery.validate.min.js"></script>
    <script src="../scripts/jquery.validate.password.js"></script>
</head>

<body>

<div id="container">
<p class="SmallHeading">Please update your password</p>

<form id="form" method="POST" action="#">
    <fieldset>
        <legend>Password Information</legend>
        <p id="pnote">Note: Passwords must be at least 8 characters long and 
            should contain a mix of characters (alpha, numeric, special). They
            are automatically set to expire in 1 year, at which time you will
            need to set a new password.</p><br />
        <label for="password">Enter a password: </label>
        <input id="passwd" type="password" name="password" size="20"
            class="password" required />&nbsp;
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
        </fieldset>
        <fieldset>
    <button id="formsubmit">Submit New Password</button>
</form>

<div id="cookie_banner">
    <h3>This site uses cookies to save member usernames</h3>
    <p>Accepting cookies allows automatic login. If you reject cookies,
    no cookie data will be collected, and you must login each visit.
    <br />You may change your decision later via the Help menu.
    </p>
    <div id="cbuttons">
        <button id="accept">Accept</button>
        <button id="reject">Reject</button>
    </div>
</div>

</div>   <!-- end of container -->
<script src="../scripts/renew.js"></script>
</body>
</html>
