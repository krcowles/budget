<?php
/**
 * This script will present the user with a list of expenses from the
 * last 30 days and he/she may select one or more to be 'undone'.
 * When the selection(s) is/are executed, the expense item(s) will be zeroed
 * and the charge(s) will be refunded to the originally charged account(s).
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require_once "../database/global_boot.php";

$paid = isset($_GET['paid']) ? true : false;
$unpaid = true;
require "get30DayExpenses.php";

$chgs = [];
for ($j=0; $j<count($eid); $j++) {
    $user_data = array(
        $eid[$j],
        "Check/Draft",
        $dte[$j],
        $amt[$j],
        $ded[$j],
        $pye[$j]
    );
    array_push($chgs, $user_data);
}
for ($k=0; $k<count($did); $k++) {
    $user_data = array(
        $did[$k],
        "Debit: " . $debname[$k],
        $dday[$k],
        $damt[$k],
        $dfrm[$k],
        $dpay[$k]
    );
    array_push($chgs, $user_data);
}
$noOfExp = count($chgs);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Undo Expense</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Undo one or more expense charges" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <link href="../styles/standards.css" type="text/css" rel="stylesheet" />
    <link href="../styles/reverseCharge.css" type="text/css" rel="stylesheet" />
 
</head>
<body>
<div style="margin-left:40px;">
<p id="expcnt" style="display:none;"><?=$noOfExp;?></p>
<h2>Select one or more expenses you wish to undo.</h2>
<h3>This will have the effect of deleting the expense and having the deleted 
    amount placed back into the account from which it was originally drawn</h3>
<?php if ($paid) : ?>
<h3 id="paid">Expense(s) Successfully Undone</h3>
<? endif; ?>
<form action="doUndo.php" method="post">
    <div>
        <button>Undo Expense</button>
        <button id="return">Return To Budget</button>
    </div>
<?php if ($noOfExp === 0) : ?>
    <h3>You have no outstanding expenses for the last 30 days</h3>
<?php else : ?>
    <div id="main">
        <div id="carddiv">
        <h3>Expenses From the Last 30 Days</h3>
        <?php for ($j=0; $j<$noOfExp; $j++) : ?>
            <div id="d<?=$chgs[$j][0];?>" style="margin-bottom:6px;">
                <input type="checkbox" name="revexp[]"
                    value="<?=$chgs[$j][0];?>" /> &nbsp;&nbsp;
                <input class="cdentry" type="text" value="<?= $chgs[$j][1];?>" />
                <input class="cdentry dates" type="text"
                    value="<?=$chgs[$j][2];?>" />
                <input class="cdentry amts" type="text"
                    name="amt<?=$chgs[$j][0];?>"
                    value="<?=$chgs[$j][3];?>" />
                <input class="cdentry" type="text" value="<?=$chgs[$j][5];?>" />
                <input class="cdentry accts" type="text" 
                    name="acc<?=$chgs[$j][0];?>"
                    value="<?=$chgs[$j][4];?>" />
            </div>
        <?php endfor; ?>
        </div>
    </div>
<?php endif; ?>
</form>
</div>
<script src="../scripts/jquery-1.12.1.js" type="text/javascript"></script>
<script src="../scripts/undoExpense.js" type="text/javascript"></script>
</body>

</html>