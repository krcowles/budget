<?php
/**
 * This module can use this script to make edits to the budget data.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
$user = filter_input(INPUT_GET, 'user');
require "../utilities/getAccountData.php";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Budget Editor</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Rolling 4-month budget tracker" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <link href="../styles/standards.css" type="text/css" rel="stylesheet" />
    <link href="../styles/newBudget.css" type="text/css" rel="stylesheet" />
    <style type="text/css">
        #page { margin-left: 24px; }
        table { border: 1px solid #ccc; border-collapse: collapse; margin: 0;
            padding: 0;}
        thead { border: 2px; border-style: solid; border-color: black; }
        th { padding: 4px; }
    </style>
</head>

<body>
<p id="user" style="display:none;"><?= $user;?></p>
<div id="page">
<span class="note NormalHeading">Note: If you make changes,
    be sure to 'Save Edits'</span><br />
<span style="font-size:18px;">You can add, delete, or rename accounts separately 
    in the main menu.</span><br /><br />
<form id="form" action="saveBudgetEdits.php" method="POST">
    <input type="hidden" name="user" value="<?= $user;?>" />
    <button id="save">Save Edits</button>
    <button id="backtobud" 
        style="margin-left:120px;">Return to Budget</button><br /><br />
    <table>
        <thead>
            <tr id="throw">
                <th>Budget Item</th>
                <th>Monthly Budget</th>
                <th>Current Balance</th>
            </tr>
        </thead>
        <tbody>
        <?php for ($j=0; $j<count($account_names); $j++) : ?>
        <tr>
            <td><?= $account_names[$j];?></td>
            <td><textarea class="bud"
                name="edbud[]"><?= $budgets[$j];?></textarea></td>
            <td><textarea class="bal"
                name="edcurr[]"><?= $current[$j];?></textarea></td>
        </tr>
        <?php endfor; ?>
        </tbody>
    </table><br />
</form>
</div>
<script src="../scripts/jquery-1.12.1.js" type="text/javascript"></script>
<script type="text/javascript">
    $('#backtobud').on('click', function(ev) {
        ev.preventDefault();
        var budget = "../main/displayBudget.php?user=" + 
            encodeURIComponent($('#user').text());
        window.open(budget, "_self");
    });
</script>

</body>

</html>
