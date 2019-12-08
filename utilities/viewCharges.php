<?php
/**
 * This utility queries the database for all the specified user's outstanding 
 * credit card charges, and displays them for viewing only.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
$user = filter_input(INPUT_GET, 'user');
require "getCards.php";
require "getExpenses.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Current Outstanding Charges</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Rolling 3-month budget tracker" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <link href="../styles/standards.css" type="text/css" rel="stylesheet" />
    <style type="text/css">
        th { text-align: left; padding: 0px 6px 0px 6px; }
        td { padding: 0px 6px 0px 6px; }
    </style>
</head>

<body>
<div>
    <h3>The following entries display the current/outstanding charges to your credit
    card(s). To edit these entries, return to the Budget page and invoke the menu
    item: <em>Expenses->Edit Charges</em>.</h3>
    <button id="back">Return To Budget</button><br />
<hr />
    <?php for ($i=0; $i<count($cr); $i++) : ?>
        <span class="SmallHeading">For credit card <?= $cr[$i];?>:</span><br />
        <table>
            <thead>
                <tr>
                    <th>Date Incurred</th>
                    <th>Amount</th>
                    <th>Payee</th>
                    <th>Deducted From</th>
                </tr>
            </thead>
            <tbody>
            <?php for ($j=0; $j<count($expamt); $j++) : ?>
                <?php if ($expcdname[$j] == $cr[$i]) : ?>
                    <tr>
                        <td><?= $expdate[$j];?></td>
                        <td>$ <?= $expamt[$j];?></td>
                        <td><?= $exppayee[$j];?></td>
                        <td><?= $expcharged[$j];?></td>
                    </tr>
                <?php endif; ?>
            <?php endfor; ?>
            </tbody>
        </table>
        <hr />
    <?php endfor; ?>
</div>
<p style="display:none" id="user"><?= $user;?></p>

<script src="../scripts/jquery-1.12.1.js" type="text/javascript"></script>
<script type="text/javascript">
    var user = $('#user').text();
    $('#back').on('click', function() {
        var budpg = "../main/displayBudget.php?user=" + user;
        window.open(budpg, "_self");
    });
</script>

</body>
</html>
