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
require_once "../utilities/getCards.php";
require_once "../utilities/getExpenses.php";
require_once "../utilities/timeSetup.php";

/**
 * The table bodies are created in php below: originally because the goal
 * was to use XHTML strict - no longer the case w/bootstrap.
 */
$counts = [];
$tbodys = [];
for ($j=0; $j<count($cr); $j++) {
    $tally = 0;
    $tbody = '<tbody>' . PHP_EOL;
    for ($k=0; $k<count($expmethod); $k++) {
        if ($expmethod[$k] === 'Credit' && $expcdname[$k] === $cr[$j]) {
            $tally++;
            $tbody .= '<tr class="trhover">' . PHP_EOL;
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
<!DOCTYPE html >
<html lang="en">
<head>
    <title>Edit Credit Active Expenses</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Rolling 3-month budget tracker" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="../styles/bootstrap.min.css" type="text/css" rel="stylesheet" />
    <link href="../styles/budgetEditor.css" type="text/css" rel="stylesheet" />
    <style type="text/css">
    textarea { height: 28px; font-size: 16px; padding-left: 5px;
            padding-bottom: 6px; }
        .dates { width: 120px; height: 22px; font-size: 16px; }
        .amt { width: 100px; }
        #main { margin-left: 24px; }
        .left { text-align: left; }
        .right { text-align: right; }
    </style>
</head>

<body>
<?php require "../main/navbar.php"; ?>
<div id="main">
    <h3>You can use this form to edit active charges
    charged to a credit card.</h3>
    <form id="form" method="post" action="saveEditedCharge.php">
    <div>
        <button id="svchgs" class="btn btn-secondary" type="button">
            Save All Changes</button>
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
</div>
<?php require "../main/bootstrapModals.html"; ?>

<script src="https://unpkg.com/@popperjs/core@2.4/dist/umd/popper.min.js"></script>
<script src="../scripts/bootstrap.min.js"></script>
<script src="../scripts/jquery-1.12.1.js" type="text/javascript"></script>
<script src="../scripts/jquery-ui.js" type="text/javascript"></script>
<script src="../scripts/dbValidation.js" type="text/javascript"></script>
<script src="../scripts/menus.js"></script>
<script type="text/javascript">
    $(function () {
        $('.datepicker').datepicker({
            dateFormat: 'yy-mm-dd'
        });
        var $amount = $('.amt');
        scaleTwoNumber($amount);
        $('#exp4').addClass('active');
        $('#svchgs').on('click', function() {
            $('form').trigger('submit');
        });
    });
</script>
</body>

</html>
