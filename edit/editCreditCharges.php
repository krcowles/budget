<?php
/**
 * This allows the user to modify expense data in the `Charges` table.
 * PHP Version 7.1
 * 
 * @package BUDGET
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require "../utilities/getCards.php";
require "../utilities/getExpenses.php";

/**
 * XHTML requires child tags in tables, so using alt control structures
 * did not yield good syntax. Hence, the table bodies are created in 
 * php below.
 */
$counts = [];
$tbodys = [];
for ($j=0; $j<count($cr); $j++) {
    $tally = 0;
    $tbody = '<tbody>' . PHP_EOL;
    for ($k=0; $k<count($expmethod); $k++) {
        if ($expmethod[$k] === 'Credit' && $expcdname[$k] === $cr[$j]) {
            $tally++;
            $tbody .= '<tr>' . PHP_EOL;
            $tbody .= "<td><input type='text' class='datepicker dates' " .
                "name='cr{$j}date[]' value='{$expdate[$k]}' /></td>" . PHP_EOL;
            $tbody .= "<td><textarea rows='1' cols='80' class='amt' " .
                "name='cr{$j}amt[]'>{$expamt[$k]}</textarea></td>" . PHP_EOL;
            $tbody .= "<td><textarea rows='1' cols='20' class='chgd' " .
                "name='cr{$j}chgd[]'>{$expcharged[$k]}</textarea></td>" . PHP_EOL;
            $tbody .= "<td><textarea  rows='1' cols='30' class='payee' " .
                "name='cr{$j}pay[]'>{$exppayee[$k]}</textarea></td>" . PHP_EOL;
            $tbody .= "</tr>" . PHP_EOL;
        }
    }
    $tbody .= '</tbody>' . PHP_EOL;
    if ($tally === 0) {
        $tbody = '<tbody><tr><td colspan="4"></td></tr></tbody>';
    }
    array_push($counts, $tally);
    array_push($tbodys, $tbody);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Edit Credit Active Expenses</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Rolling 3-month budget tracker" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <link href="../styles/standards.css" type="text/css" rel="stylesheet" />
    <link href="../styles/charges.css" type="text/css" rel="stylesheet" />
    <link href="../styles/jquery-ui.css" type="text/css" rel="stylesheet" />
    <style type="text/css">
        textarea { height: 24px; font-size: 16px; padding-top: 4px; }
        .dates { width: 120px; height: 22px; font-size: 16px; }
        .amt { width: 100px;}
        #main { margin-left: 24px; }
        .left { text-align: left }
        .right { text-align: right }
    </style>
</head>

<body>
<div id="main">
    <p class="NormalHeading">You can use this form to edit active charges
    charged to a credit card.</p>
    <form id="form" method="post" action="saveEditedCharge.php">
    <div>
        <button id="save">Save All Changes</button>
        <button id="return" style="margin-left:80px;">Return to Budget</button>
        <br /><br />
        <div id="existing">
        <?php for ($i=0; $i<count($cr); $i++) : ?>
            <input type="hidden" name="cnt[]" value="<?= $counts[$i]?>" />
            <span class="BoldText">These are your current charges against
                <?= $cr[$i];?>
            </span>
            <table>
                <thead>
                    <tr>
                        <th>Date:</th>
                        <th>Amount</th>
                        <th>Deducted From:</th>
                        <th>Payee:</th>
                    </tr>
                </thead>
                <?=$tbodys[$i];?>
            </table><br />
        <?php endfor; ?>
        </div>
    </div>
    </form>
    <p style="clear:left;margin-left:16px;">
        <a href="http://validator.w3.org/check?uri=referer">
            <img src="http://www.w3.org/Icons/valid-xhtml10"
            alt="Valid XHTML 1.0 Strict" height="31" width="88" />
        </a>
    </p>
</div>

<script src="../scripts/jquery-1.12.1.js" type="text/javascript"></script>
<script src="../scripts/jquery-ui.js" type="text/javascript"></script>
<script src="../scripts/dbValidation.js" type="text/javascript"></script>
<script type="text/javascript">
    $('#return').on('click', function(ev) {
        ev.preventDefault();
        var dpg = "../main/displayBudget.php";
        window.open(dpg, "_self");
    });
    $(function () {
        $('.datepicker').datepicker({
            dateFormat: 'yy-mm-dd'
        });
        var $amount = $('.amt');
        scaleTwoNumber($amount);
    });
</script>
</body>

</html>
