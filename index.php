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
<!DOCTYPE html>
<html lang="en-us">
<head>
    <title>The Budgetizer</title>
    <meta charset="utf-8" />
    <meta name="description" content="Mobile site for New Mexico Hikes" />
    <meta name="author" content="Ken Cowles" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous" />
    <link href="index.css" type="text/css" rel="stylesheet" />
    <script src="../scripts/jquery-1.12.1.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
</head>

<body>
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
                            placeholder="Password" autocomplete="password" />
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
            <p>Or:&nbsp;&nbsp;<a href="admin/registration.html">
                sign up and start your new budget!</a></p>
        </div>
    </div>
</div>
<div>
    <p id="cookiestatus" style="display:none;"><?=$cstat?></p>
    <p id="startpg" style="display:none;"><?=$start;?></p>
</div>

<div id="resetemail" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Password</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="ap" class="modal-body">
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

<script src="scripts/getLogin.js"></script>
<script src="index.js"></script>

</body>
</html>