<?php
/**
 * This page allows the user to return to budget creation
 * by clicking a link.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require "../database/global_boot.php";
$current = $_SESSION['start'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Budgetizer Exit Page</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Rolling 3-month budget tracker" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <link href="../styles/standards.css" type="text/css" rel="stylesheet" />
</head>

<body>
<div style="margin-left:32px;margin-top:24px;">
    <h2>Thanks for using the Budgetizer!</h2>
    <h3>When you return to this site, you may resume data entry where 
        you left off.
    </h3>
    <h3>You may return to the program now: 
        <a href="../edit/newBudgetPanels.php?pnl=<?=$current;?>"
            target="_self">Click to Resume Data Entry</a>
    </h3>
</div>
<p style="clear:left;margin-left:16px;">
    <a href="http://validator.w3.org/check?uri=referer">
        <img src="http://www.w3.org/Icons/valid-xhtml10"
        alt="Valid XHTML 1.0 Strict" height="31" width="88" />
    </a>
</p>
</body>
</html>
