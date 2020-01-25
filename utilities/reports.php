<?php
/**
 * This utility will generate a monthly or annual report, where the user
 * specifies the parameters. All expenses, paid or not, will be displayed.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
$user = filter_input(INPUT_GET, 'user');
$id = isset($_GET['id']) ? filter_input(INPUT_GET, 'id') : false;
$monthly = $id = 'morpt' ? true : false;
$annual  = $id = 'yrrpt' ? true : false;
if ($monthly) {
    $period = isset($_GET['mo']) ? filter_input(INPUT_GET, 'mo') : false;
} elseif ($annual) {
    $period = isset($_GET['yr']) ? filter_input(INPUT_GET, 'yr') : false;
}
require "getCards.php";
require "timeSetup.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>User Report</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Rolling 3-month budget tracker" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <link href="../styles/standards.css" type="text/css" rel="stylesheet" />
    <style type="text/css">
       #page { margin-left: 16px; }
       #back { margin-left: 10px; }
       table { border-collapse: collapse; background-color: #fffaf0;
               border-width: 2px; border-style: outset; border-color: black; }
       th { text-align: left; padding: 4px 6px 4px 6px;
            background-color: floralwhite; border-bottom: 2px 
            solid black; border-top: 2px solid black; }
        tr.even { background-color: #ffeecc; }
       td { padding: 4px 6px 4px 6px; }
       .red  { color: firebrick; }
    </style>
</head>

<body>
<div id="page">
    <p id="user" style="display:none;"><?= $user;?></p>
    <button id="back">Return to Budget</button>
    <?php
    if ($monthly) {
        include "formatMonth.php";
    } else if ($annual) {
        include "formatYear.php";
    }
    ?>
    <p style="clear:left;margin-left:16px;">
        <a href="http://validator.w3.org/check?uri=referer">
            <img src="http://www.w3.org/Icons/valid-xhtml10"
            alt="Valid XHTML 1.0 Strict" height="31" width="88" />
        </a>
    </p>
</div>

<script src="../scripts/jquery-1.12.1.js" type="text/javascript"></script>
<script type="text/javascript">
    $('#back').on('click', function() {
        var budpg = '../main/displayBudget.php?user=' + 
            '<?= rawurlencode($user);?>';
        window.open(budpg, "_self");
    });
</script>

</body>
</html>
