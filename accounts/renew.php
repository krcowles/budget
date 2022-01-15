<?php
/**
 * This script will allow the user to assign, change, or renew his/her password.
 * There are multiple paths leading to the execution of this script:
 *  -- when a visitor is registering an account, an email is sent with a one-time
 *     code and userid ($_GET['reg'] is also set)
 *  -- a user may either click on 'Forgot Password', or request a change to his/her
 *     password (MyAccount->Change Password), directing the user to this page
 *     via an email link and a one-time code and userid ($_GET['reg'] is not set)
 *  -- when a 'renew' is the result of logging in (either via cookies or via 
 *     login), session variables are already set.
 * PHP Version 7.4
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require "../database/global_boot.php";
require "../accounts/security_questions.php";

$new = isset($_GET['reg']) ? true : false;
$jsnew = $new ? 'new' : 'not';
if (isset($_GET['code'])) {
    // new registration or forgot/change password, 'code' & 'ix' are both set in url 
    $tmpcode = filter_input(INPUT_GET, 'code');
    $user = filter_input(INPUT_GET, 'ix');
    $form_type = 'login';
} else {
    // renew via logging in => session variables already assigned
    $user = $_SESSION['userid'];
    $form_type = 'nolog';
}

if (!$new) {
    $getQsAndAsReq = "SELECT `questions`,`answers` FROM `Users` WHERE `uid`=?;";
    $getQsAndAs = $pdo->prepare($getQsAndAsReq);
    $getQsAndAs->execute([$user]);
    $UQAs = $getQsAndAs->fetch(PDO::FETCH_ASSOC);
    $Qs = explode(",", $UQAs['questions']);
    $As = explode(",", $UQAs['answers']);
}
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
    <title><?php if ($new) : ?>Account Registration
           <?php else : ?>Login Reset
           <?php endif; ?>
    </title>
    <meta charset="utf-8" />
    <meta name="description" content="User password entry/update" />
    <meta name="author" content="Tom Sandberg and Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="../styles/bootstrap.min.css" type="text/css" rel="stylesheet" />
    <link href="../styles/standards.css" type="text/css" rel="stylesheet" />
    <link href="../styles/renew.css" type="text/css" rel="stylesheet" />
</head>

<body>
<script src="../scripts/bootstrap.min.js"></script>

<!-- Password Status Details Modal -->
<div id="show_pword_details" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Password Status</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table id="ptable">
                    <tbody>
                        <tr>
                            <td>Characters:</td>
                            <td id="total">0</td>
                            <td colspan="6"></td>
                        </tr>
                        <tr>
                            <td>Lower case:</td>
                            <td id="lc">0</td>
                            <td>Upper case:</td>
                            <td id="uc">0</td>
                            <td>Numbers:</td>
                            <td id="nm">0</td>
                            <td>Special:</td>
                            <td id="sp">0</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Security Questions Modal -->
<div id="security" class="modal" tabindex="-1">
    <div class="modal-dialog" style="max-width:60%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Answer 3 Security Questions</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

            <?php if (!$new) : ?>
                <?php for ($j=0, $a=0; $j<count($questions); $j++) : ?>
                    <span class="ques"><?=$questions[$j];?></span>
                    <?php if (in_array($j, $Qs)) : ?>
                        <input id="q<?=$j;?>" style="padding-left:6px;"
                        type="text" name="ans[]" value="<?=$As[$a++];?>" /><br />
                    <?php else : ?>
                        <input id="q<?=$j;?>" style="padding-left:6px;" 
                        type="text" name="ans[]" /><br />
                    <?php endif; ?>
                <?php endfor; ?>
            <?php else : ?>
                <?php for ($k=0; $k<count($questions); $k++) : ?>
                    <span class="ques"><?=$questions[$k];?></span>
                    <input id="q<?=$k;?>" style="padding-left:6px;" 
                        type="text" name="ans[]" /><br />
                <?php endfor; ?>
            <?php endif; ?>

            </div>
            <div class="modal-footer">
                <button id="resetans" type="button" class="btn btn-secondary">
                    Reset Answers</button>
                <button id="closesec" type="button" class="btn btn-secondary">
                    Apply</button>
            </div>
        </div>
    </div>
</div>

<div id="container">
    <p id="new" style="display:none;"><?=$jsnew;?></p>
    <p id="logstat" style="display:none;"><?=$form_type;?></p>
    <h3 id="header">Please enter and confirm a new password:</h3>
    <form id="form" method="POST" action="create_user.php">
        <div id="inputs">
            <input type="hidden" name="submitter"  value="change" />
            <input id="usr" type="hidden" name="userid" value="<?=$user;?>" />
            <input id="usrchoice" type="hidden" name="cookies" value="nochoice" />
            <div id="pexpl">
                **&nbsp;Your password must be 11 characters or more and contain
                upper and lower case letters and at least 1 number and 1 special
                character (no spaces).
            </div>
            <div id="newbie">
                <?php if (isset($tmpcode)) : ?>
                <span class="cola otc">One-time Code:</span>
                <input class="colb otc" type="password" name="oldpass" size="20"
                        autocomplete="off" value="<?=$tmpcode;?>" /><br />
                <?php endif; ?>
                <span class="cola">New password:</span>
                <input class="colb weak" id="pword" type="password"
                    name="password" size="20" autocomplete="new-password" />
                <div id="pwddiv" class="colc">
                    <div id="usrinfo">
                        <span id="wk">Weak</span>
                        <span id="st">Strong</span>&nbsp;&nbsp;
                        <button id="showdet">Show Why</button>
                    </div><br />
                    <span id="showit">Show Password:&nbsp;&nbsp;<input id="ckbox" 
                        type="checkbox" /></span>
                </div>
                <span class="cola">Confirm:</span>
                <input id="confirm" class="colb" type="password" name="confirm" 
                    autocomplete="new-password" size="20" /><br /> 
            </div>
        </div>    
        <span id="link"><a id="sq" href="#">
            <?php if ($new) : ?>Select 3 Security Questions
            <?php else : ?>Review/Change Security Questions
            <?php endif; ?></a>
        </span><br /><br />
        <button id="formsubmit">Click Once</button>
    </form>
</div>   <!-- end of container -->

<div id="cookie_banner">
    <h3>This site uses cookies to save member usernames only</h3>
    <p>Accepting cookies allows automatic login. If you reject cookies,
    no cookie data will be collected, and you must login each visit.
    <br />You may change your decision later via the Help menu.
    </p>
    <div id="cbuttons">
        <button id="accept">Accept</button>
        <button id="reject">Reject</button>
    </div>
</div>

<script src="../scripts/jquery.min.js"></script>
<script src="../scripts/renew.js"></script>
<script src="../scripts/passwordStrength.js"></script>
</body>
</html>
