<?php
/**
 * This page has three distinct sections, each displaying slightly different forms.
 * 1. A registration form;
 * 2. A password reset form for a 'Change password' request, a 'Forgot password'
 *    request, a membership renewal, or to complete registration; Security questions
 *    may be selected or changed;
 * 3. A login form.
 * In the case of a normal user login, e.g. a user having rejected cookies or having
 * previously logged out, the only information required is username/password.
 * Otherwise, the user has received an email with a one-time secure code, and a link
 * to the page. The user will be required to select a new password to continue and 
 * if not already done, select security questions and answers. If already selected,
 * they may be changed here.
 * PHP Version 7.4
 * 
 * @package MedRefs
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No License to date
 */
session_start();
require "../php/global_boot.php";
$form   = filter_input(INPUT_GET, 'form');
$code   = isset($_GET['code']) ? filter_input(INPUT_GET, 'code') : '';
$ix     = isset($_GET['ix']) ? filter_input(INPUT_GET, 'ix') : false;
$newusr = isset($_GET['reg']) ? true : false;
if ($form === 'reg') {
    $title = "Registration Form";
} elseif ($form === 'pwd_reset') {
    $title = "Set Password";
} elseif ($form === 'log') {
    $title = "Log in";
}
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
    <!-- there is no navbar on this page -->
    <title><?=$title;?></title>
    <meta charset="utf-8" />
    <meta name="description" content="Unified login page" />
    <meta name="author" content=" Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="../styles/bootstrap.min.css" rel="stylesheet" />
    <link href="unifiedLogin.css" type="text/css" rel="stylesheet" />
    <script type="text/javascript">
        var page = 'unified';
        var isMobile, isTablet, isAndroid, isiPhone, isiPad;
        window.addEventListener("load", () => { // TRUE OR NULL!
            var isMobile = navigator.userAgent.toLowerCase().match(/mobile/i) ? 
                true : false;
            var isTablet = navigator.userAgent.toLowerCase().match(/tablet/i) ?
                true : false;
            var isAndroid = navigator.userAgent.toLowerCase().match(/android/i) ?
                true : false;
            var isiPhone = navigator.userAgent.toLowerCase().match(/iphone/i) ?
                true : false;
            var isiPad = navigator.userAgent.toLowerCase().match(/ipad/i) ?
                true : false;
        });
        var mobile = isMobile && !isiPad && !isTablet;
    </script>
    <script src="../scripts/jquery.min.js"></script>
</head>

<body>
<script src="https://unpkg.com/@popperjs/core@2.4/dist/umd/popper.min.js"></script>
<script src="../scripts/bootstrap.min.js"></script>
<!-- only the logo is presented on this page, no navbar -->
<div id="bg">
</div>

<div id="logo">
    <div id="pgheader">
        <div id="leftside">
            <img id="leftie" src="../images/medleft.png"/>
            &nbsp;&nbsp;<span id="ltxt">Medical References</span>
        </div>
        <div id="center"><?=$title;?></div>
        <div id="rightside">
            <span id="rtxt">Personalized Data</span>&nbsp;&nbsp;
            <img id="rightie" src="../images/medright.png" />
        </div>
    </div>   
</div>

<p id="appMode" class="noshow"><?=$appMode;?></p>
<p id="formtype" class="noshow"><?=$form;?></p>
<div id="container">  <!-- only one of the three sections will appear on page -->
<?php if ($form === 'reg') : ?>
    <form id="form" action="#" method="post">
        <input type="hidden" name="submitter" value="create" />
        <p id="reg_hdr">Sign up for free access to MedRefs<br />
        <a id="policylnk" href="#">Privacy Policy</a>
        </p>
        <div class="mobinp">
            <div class="pseudo-legend">First Name</div>
            <div id="line1" class="lines"></div>
            <input id="fname" type="text" class="wide"
                placeholder="First Name" name="firstname"
                autocomplete="given-name" required />
        </div>
        <div class="mobinp">
            <div class="pseudo-legend">Last Name</div>
            <div id="line2" class="lines"></div>
            <input id="lname" type="text" class="wide"
                placeholder="Last Name" name="lastname"
                autocomplete="family-name" required />
        </div>
        <div id="un_note" class="mobtxt"><span>Username must be 6
            characters min, no spaces</span>
        </div>
        <div id="un" class="mobinp">
            <div class="pseudo-legend">Username</div>
            <div id="line3" class="lines"></div>
            <input id="uname" type="text" class="wide"
                placeholder="User Name" name="username"
                autocomplete="username" required />
        </div>
        <div id="uem" class="mobinp">
            <div class="pseudo-legend">Email</div>
            <div id="line4" class="lines"></div>
            <input id="umail" type="email" class="wide"
                required placeholder="Email" name="email"
                autocomplete="email" /><br /><br />
        </div>
        <div class="mobinp">
            <button id="formsubmit">Submit (Click Once)</button>
        </div> 
    </form>
<?php elseif ($form === 'pwd_reset') : ?>
    <?php if ($newusr) : ?>
        <h3>Set Account Password</h3>
    <?php else : ?>
        <h3>Reset Passsword:</h3>
    <?php endif; ?>
    <form id="form" action="#" method="post">
        <input type="hidden" name="code" value="<?=$code;?>" />
        <?php if ($ix !== false) : ?>
            <p id="ix" style="display:none;"><?=$ix;?></p>
        <?php else : ?>
            <p><strong>ERROR: missing uid</strong></p>
        <?php endif; ?>
        <input id="usrchoice" type="hidden" name="cookies" 
            value="nochoice" class="wide" />
        <span class="mobtxt">Temporary passcode</span>
        <input id="one-time" type="password" name="one-time" autocomplete="off"
            value="<?=$code;?>" class="wide" /><br /> 
        <div id="pexpl">
            **&nbsp;Your new password must be 10 characters or more and contain
            upper and lower case letters and at least 1 number and 1 special
            character.
        </div>
        <div id="pass">
            <input id="password" type="password" name="password"
                autocomplete="new-password" required class="wide renpass"
                placeholder="New Password" /><br />
            <div id="usrinfo">
                <span id="wk">Weak</span>
                <span id="st">Strong</span>&nbsp;&nbsp;
                <button id="showdet">Show Why</button>&nbsp;&nbsp;
                Show password&nbsp;&nbsp;&nbsp;
                <input id="ckbox" type="checkbox" /><br /><br />
            </div>
        </div> 
        <input id="confirm" type="password" name="confirm" class="wide mobinp"
            autocomplete="new-password" required="required"
            placeholder="Confirm Password" /><br />
        <div>
            <?php if ($newusr) : ?>
                <a id="rvw" href="#">Select Your Security Questions</a>
            <?php else : ?>
                <a id="rvw" href="#">Review/Edit Security Questions</a>
            <?php endif; ?>
        </div><br />
        <button type="submit" id="formsubmit" class="btn mobinp">
            Submit</button>     
    </form>
<?php elseif ($form === 'log') : ?>
    <div class="container">
        <h3 id="hdr">Member Log in</h3><br />
        <form id="form" action="#" method="post">
            <input id="usrchoice" type="hidden" name="cookies"
                value="nochoice" />
            <input class="logger wide" id="username" type="text"
                placeholder="Username" name="username" autocomnplete="username"
                required /><br /><br />
            <input class="logger wide" id="password" type="password"
                name="oldpass" placeholder="Password" size="20"
                autocomplete="password" required/><br /><br />
            <button id="logger" type="button" class="btn btn-outline-secondary"
                data-bs-toggle="modal" data-bs-target="#cpw" onclick="this.blur();">
                Forgot Username/Password?
            </button>
            <button id="formsubmit" type="submit" class="btn btn-secondary">
                Submit
            </button>
        </form>
    </div>
<?php endif; ?>
</div>   <!-- end of #container -->
<?php require "../pages/security_modals.html"; ?>

<script src="../scripts/logo.js"></script>
<script src="validateUser.js"></script>
<script src="passwordStrength.js"></script>
<script src="unifiedLogin.js"></script>

</body>
</html>
