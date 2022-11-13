<?php
/**
 * If the panel has determined that a session no longer exists, this
 * page will alert the user to that effect.
 * PHP Version 7.4
 * 
 * @package MedRefs
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../php/global_boot.php";
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
    <title>Session Expired</title>
    <meta charset="utf-8" />
    <meta name="description" content="User session has expired" />
    <meta name="robots" content="nofollow" />
    <link href="../styles/ktesaNavbar.css" type="text/css" rel="stylesheet" />
    <style type="text/css">
        body { background-color: #eaeaea; }
        #msg { margin-left: 24px; }
    </style>
</head>
<body>

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

<div id="msg">
    <h2>Your login session has expired</h2>
    <h3>[Not on mobile devices:] If you have accepted cookies, you
        may automatically re-login: 
        <a href="../pages/main.php" target="_self">Click here</a></h3>
    <h3>[Mobile or other:] If you have rejected cookies (or don't
        remember), use this link to log in:
        <a href="unifiedLogin.php?form=log" target="_self">Login Page</a></h3>
</div>

</body>
</html>