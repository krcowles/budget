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
require "accounts/getLogin.php";
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
    <title>The Budgetizer</title>
    <meta charset="utf-8" />
    <meta name="description" content="Mobile site for New Mexico Hikes" />
    <meta name="author" content="Ken Cowles" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="../styles/bootstrap.min.css" type="text/css" rel="stylesheet" />
    <link href="index.css" type="text/css" rel="stylesheet" />
    <script src="../scripts/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsencrypt/3.1.0/jsencrypt.min.js" integrity="sha512-Tl9i44ZZYtGq56twOViooxyXCSNNkEkRmDMnPAmgU+m8B8A8LXJemzkH/sZ7y4BWi5kVVfkr75v+CQDU6Ug+yw==" crossorigin="anonymous">
</script>
</head>

<body>
<!-- You CANNOT place bootstrap in the <head> element! -->

<script src="../scripts/bootstrap.min.js"></script>
<div id="top-part">
    <div id="sitetext">
        <span id="maintext">The Budgetizer</span><br />
        <span id="subtext">A home budget <br />creation and management tool</span>
    </div><div id="money">
    </div>
</div>

<div id="bottom-part">
    <div id="info">
        <div id="learn">
            <span id="lrn">Beginner?<br />Learn <a
            href="help/help.php?doc=HowToBudget.pdf" target="_blank">
            How to Budget</a></span>
        </div>
        <div id="tools">
            <span id="tls">See also, how to use<br /><a
            href="help/help.php?doc=Tools.pdf" target="_blank">
            Budgetizer Tools</a></span>
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
                            placeholder="Password" autocomplete="current-password" />
                        </td>
                    </tr>
                    <tr class="spacer">
                        <td></td>
                        <td></td>
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
                    <tr class="spacer">
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td id="forgot">
                            <a id="resetpass"
                                href="#">Forgot Username or Password?</a>
                        </td>
                       
                    </tr>
                </tbody>
            </table>
            </form>
        </div>
        <div id="register">
            <p>Or:&nbsp;&nbsp;<a href="accounts/registration.php">
                Sign up and start your new budget!</a></p>
        </div>
    </div>
</div>

<div> <!-- login status variables passed to javascript -->
    <p id="cookiestatus" style="display:none;"><?=$cstat?></p>
    <p id="startpg" style="display:none;"><?=$start;?></p>
</div>

<!--                             Modals                            -->
<!-- Security Question Modal -->
<div id="twofa" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Security Question</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="ap" class="modal-body">
               <p id="the_question"></p>
               <input id="the_answer" type="text" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Close</button>
                <button id="submit_answer" type="button"
                    class="btn btn-success">Apply</button>
            </div>
        </div>
    </div>
</div>
<!-- Email Modal -->
<div id="resetemail" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Password</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="ap" class="modal-body">
                <p id="passtype" style="display:none;">lockout</p>
                You will be sent an email with your account name and a link
                to reset your password.<br />
                Your email: <input id="remail" type="email" />
            </div>
            <div class="modal-footer">
                <button id="apmodbtn" type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Close</button>
                <button id="cpass" type="button"
                    class="btn btn-success">Send Email</button>
            </div>
        </div>
    </div>
</div>

<script src="index.js"></script>
<script src="scripts/getLogin.js"></script>

</body>
</html>