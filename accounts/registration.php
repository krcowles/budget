<?php
/**
 * This page allows a new member to register. An email will be sent
 * to the registrant with a one-time security code and a link to the
 * password setting page. 
 * PHP Version 7.4
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require_once "security_questions.php";
?>
<!DOCTYPE html>
<html lang="en-us">

<head>
    <title>New User Registration</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description" content="New user sign-up" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="../styles/bootstrap.min.css" type="text/css" rel="stylesheet" />
    <link href="../styles/standards.css" type="text/css" rel="stylesheet" />
    <link href="../styles/jquery-ui.css" type="text/css" rel="stylesheet" />
    <link href="../styles/modals.css" type="text/css" rel="stylesheet" />
    <link href="../styles/registration.css" type="text/css" rel="stylesheet" />
</head>

<body>

<div id="ctr">
    <div id="header">
        <h2 class="NormalHeading">
            Welcome to the Budgetizer Website!
        </h2>
        <p id="stmnt" class="SmallHeading">Fill out the items below to become
            a (free) registered user. Only the minimum information is required
            to secure your membership.
        </p>
    </div>
</div>

<div id="container">
    <form id="form" action="create_user.php" method="post">
        <input type="hidden" name="submitter" value="create" />
        <input id="usrchoice" type="hidden" name="cookies" value="nochoice" />
        <div id="registration">
            <p id="sub">Create and manage your personal budget</p>
            <div class="user-input leftmost">
                <div class="rules">Username must be<br />at least 6 characters </div>
                <div class="pseudo-legend">Username</div>
                <div id="line3" class="lines"></div>
                <input id="uname" class="signup" type="text"
                    placeholder="User Name" name="username"
                    autocomplete="off" />
            </div>
            <div class="user-input">
                <div class="pseudo-legend">Email</div>
                <div id="line4" class="lines"></div>
                <input id="email" class="signup" type="email"
                    placeholder="Email" name="email"
                    autocomplete="email" />
            </div><br />
            <div id="pexpl">
                Once you click on 'Submit', an email will be sent containing
                a security code and a link to create your password. You may then
                login as a registered user!
            </div>
            <button id="submit">Submit</button><br />
            <a id="policy" href="../help/help.php?doc=PrivacyPolicy.pdf"
                target="_blank">Privacy Policy</a> 
        </div>
    </form>
</div>

<script src="https://unpkg.com/@popperjs/core@2.4/dist/umd/popper.min.js"></script>
<script src="../scripts/bootstrap.min.js"></script>
<script src="../scripts/jquery.min.js"></script>
<script src="../scripts/registration.js"></script>

</body>
</html>