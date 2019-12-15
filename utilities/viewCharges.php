<?php
/**
 * This utility queries the database for all the specified user's outstanding 
 * credit card charges, as well as the paid checks/drafts from the previous
 * 30 days, and displays them for viewing only.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
$user = filter_input(INPUT_GET, 'user');
require "getCards.php";
require "getExpenses.php";
require "get30DayExpenses.php";
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
        table { border-collapse: collapse; }
        th { text-align: left; padding: 0px 6px 0px 6px;
            border: 1px solid #ddd; background-color: #B8D2AF; }
        td { padding: 0px 6px 0px 6px; border: 1px solid #ddd; }
        .col1 { width: 120px; }
        .col2 { width: 100px; }
        .col3, .col4 { width: 180px; }
        #ochgs, #exps { width: 50%; float: left; }
        #innerexp { position: absolute; }
    </style>
</head>

<body>
<div id="ochgs">
    <h3>The following entries display the
        current/outstanding charges to your <em style="color:brown;">credit
        card(s)</em>. To edit these entries, return to the Budget page and invoke
        the menu item: <em>Expenses->Edit Charges</em>.
    </h3>
    <button id="back">Return To Budget</button><br />
    <hr id="barloc" />
    <?php for ($i=0; $i<count($cr); $i++) : ?>
        <span class="SmallHeading">For credit card <em style="color:brown;">
            <?= $cr[$i];?></em>:</span><br />
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
                        <td class="col1"><?= $expdate[$j];?></td>
                        <td class="col2">$ <?= $expamt[$j];?></td>
                        <td class="col3"><?= $exppayee[$j];?></td>
                        <td class="col4"><?= $expcharged[$j];?></td>
                    </tr>
                <?php endif; ?>
            <?php endfor; ?>
            </tbody>
        </table>
        <hr />
    <?php endfor; ?>
</div>
<div id="exps">
    <h3 style="padding-left:4px;">The following entries display paid
        <em style="color:brown;">check,
        draft, or debit card</em> expenses for the previous 30-day period.
    </h3>
    <div id="innerexp">
        <hr />
        <span class="SmallHeading">Checks/Drafts:</span>
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
            <?php for ($j=0; $j<count($amt); $j++) : ?>
                <tr>
                    <td class="col1"><?= $dte[$j];?></td>
                    <td class="col2"><?= $amt[$j];?></td>
                    <td class="col3"><?= $pye[$j];?></td>
                    <td class="col4"><?= $ded[$j];?></td>
                </tr>
            <?php endfor; ?>
            </tbody>
        </table>
        <hr />
        <?php for ($i=0; $i<count($dr); $i++) : ?>
            <span class="SmallHeading">For debit card <em style="color:brown;">
                <?= $dr[$i];?></em>:</span><br />
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
                <?php for ($j=0; $j<count($damt); $j++) : ?>
                    <?php if ($debname[$j] == $dr[$i]) : ?>
                    <tr>
                        <td class="col1"><?= $dday[$j];?></td>
                        <td class="col2"><?= $damt[$j];?></td>
                        <td class="col3"><?= $dpay[$j];?></td>
                        <td class="col4"><?= $dfrm[$j];?></td>
                    </tr>
                    <?php endif; ?>
                <?php endfor; ?>
                </tbody>
            </table>
            <hr />
        <?php endfor; ?>
    </div>
</div>

<p style="display:none" id="user"><?= $user;?></p>

<script src="../scripts/jquery-1.12.1.js" type="text/javascript"></script>
<script type="text/javascript">
    var user = $('#user').text();
    $('#back').on('click', function() {
        var budpg = "../main/displayBudget.php?user=" + user;
        window.open(budpg, "_self");
    });
    positionExpenses();
    function positionExpenses() {
        var divloc = $('#exps').offset();
        var hrpos = $('#barloc').offset();
        $('#innerexp').css({
            top: hrpos.top - 8,
            left: divloc.left
        });
    }
    $(window).resize(positionExpenses);
</script>

</body>
</html>
