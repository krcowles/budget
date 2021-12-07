<?php
/**
 * This page allows the user to return to budget creation
 * by clicking a link.
 * PHP Version 7.3
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require "../database/global_boot.php";
$current = $_SESSION['start'];
?>
<!DOCTYPE html>
<html lang="en-us">

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

</body>
</html>
