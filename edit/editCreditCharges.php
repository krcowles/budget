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
require_once "../utilities/getAccountData.php";
require_once "../utilities/getCards.php";
require_once "../utilities/getExpenses.php";
require_once "../utilities/timeSetup.php";


/**
 * The table bodies are created in php below: originally because the goal
 * was to use XHTML strict - no longer the case w/bootstrap.
 */
$counts = [];
$tbodys = [];
// Set up an array of strings for javascript which id's the initialized value
// for select boxes in each credit card table
$crsels = '["';
for ($j=0; $j<count($cr); $j++) {
    $tally = 0;
    $vals = [];
    $tbody = '<tbody>' . PHP_EOL;
    for ($k=0; $k<count($expmethod); $k++) {
        if ($expmethod[$k] === 'Credit' && $expcdname[$k] === $cr[$j]) {
            $tally++;
            array_push($vals, $expcharged[$k]);
            $tbody .= '<tr class="trhover">' . PHP_EOL;
            $tbody .= "<td><input type='text' class='datepicker dates' " .
                "name='cr{$j}date[]' value='{$expdate[$k]}' /></td>" . PHP_EOL;
            $tbody .= "<td><textarea rows='1' cols='80' class='amt' " .
                "name='cr{$j}amt[]'>{$expamt[$k]}</textarea></td>" . PHP_EOL;
            $tbody .= "<td>{$fullsel}</td>" . PHP_EOL;
            $tbody .= "<td><textarea  rows='1' cols='30' class='payee' " .
                "name='cr{$j}pay[]'>{$exppayee[$k]}</textarea></td>" . PHP_EOL;
            $tbody .= "</tr>" . PHP_EOL;
        }
    }
    // Id items to be initialized by javascript in the 'Deducted From' column:
    $crsels.= implode("|", $vals);
    $crsels .= $j === count($cr) - 1 ? '"]' : '","';
    $tbody .= '</tbody>' . PHP_EOL;

    if ($tally === 0) {
        $tbody = '<tbody><tr><td colspan="4"></td></tr></tbody>';
    }
    array_push($counts, $tally);
    array_push($tbodys, $tbody);
}
if (count($cr) === 0) {
    $js_sels = '[]';
} else {
    $js_sels = $crsels;
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
    <link href="../styles/jquery-ui.css" type="text/css" rel="stylesheet" />
    <link href="../styles/budgetEditor.css" type="text/css" rel="stylesheet" />
    <link href="../styles/modals.css" type="text/css" rel="stylesheet" />
    <link href="../styles/creditChargeEditor.css" type="text/css" rel="stylesheet" />
</head>

<body>
<?php require "../main/navbar.php"; ?>
<div id="main">
    <br />
    <h4>You can use this form to edit active (not yet paid) charges
    charged to your credit card(s).</h4>
    <form id="form" method="post" action="saveEditedCharge.php">
    <div>
        <button id="svchgs" class="btn btn-secondary" type="button">
            Save All Changes</button>
        <br /><br />
        <div id="existing">
        <?php for ($i=0; $i<count($cr); $i++) : ?>
            <input type="hidden" name="cnt[]" value="<?= $counts[$i]?>" />
            <h4>These are your current charges against <?= $cr[$i];?></h4>
            <h5>Click on Header to sort; again to reverse</h5>
            <table class="sortable">
                <thead>
                    <tr>
                        <th data-sort="inp">Date:</th>
                        <th data-sort="amt">Amount</th>
                        <th data-sort="sel">Deducted From:</th>
                        <th data-sort="std">Payee:</th>
                    </tr>
                </thead>
                <?=$tbodys[$i];?>
            </table><br />
        <?php endfor; ?>
        </div>
    </div>
    </form>
</div>
<?php require "../main/bootstrapModals.php"; ?>

<script src="https://unpkg.com/@popperjs/core@2.4/dist/umd/popper.min.js"></script>
<script src="../scripts/bootstrap.min.js"></script>
<script src="../scripts/jquery.min.js"></script>
<script src="../scripts/jquery-ui.js"></script>
<script src="../scripts/dbValidation.js"></script>
<script src="../scripts/menus.js"></script>
<script>var selinits = <?=$js_sels;?>;</script>
<script src="../scripts/creditChargeEditor.js"></script>
<script src="../scripts/tableSort.js"></script>
</body>

</html>
