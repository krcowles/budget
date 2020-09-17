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
session_start();

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
    <link href="../styles/manageExp.css" type="text/css" rel="stylesheet" />
</head>

<body>

<!-- menu -->
<div id="actions">
    <a id="edcred" class="sel">Edit Credit Data</a>
    <a id="edexp" class="sel">Edit Expenses/Debits</a>
    <div id="mv">MOVE:</div>
    <a id="e2c" class="sel">Expense/Debit to CrCard</a>
    <a id="c2c" class="sel">One CrCard to Another</a>
    <a id="c2e" class="sel">CrCard to Expense/Debit</a>
</div>
<pre><br /></pre>
<!-- end of menu -->

<div id="ochgs">
    <h3>The following entries display the
        current/outstanding charges to your <em style="color:brown;">credit
        card(s)</em>.
    </h3>
    <button id="back">Return To Budget</button><br />
    <hr id="barloc" />
    <?php for ($i=0; $i<count($cr); $i++) : ?>
        <span class="SmallHeading">For credit card <em style="color:brown;">
            <?= $cr[$i];?></em></span>:&nbsp;&nbsp;&nbsp;
            <span class="mvtocr"><strong class="disptocr">To:</strong> 
            <input id="tocr<?= $i;?>" type="checkbox" /></span><br />
        <table class="crtbl">
            <thead>
                <tr>
                    <th>Date Incurred</th>
                    <th>Amount</th>
                    <th>Payee</th>
                    <th>Deducted From</th>
                    <th class="mvcr hdcr">Mv</th>
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
                        <td class="mvcr"><input id="cr<?= $expid[$j];?>"
                            type="checkbox" /></td>
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
        draft, or debit card</em> expenses for the previous 30-day period.</h3>
        <button id="canc">Cancel This Transfer</button>
    <div id="innerexp">
        <hr id="exphr" />
        <span class="SmallHeading">Checks/Drafts:</span>&nbsp;&nbsp;&nbsp;
        <span class="mvtodr"><strong class="disptodr">To:</strong> 
        <input id="todrcheck" type="checkbox" /></span><br />
        <table class="drtbl">
            <thead>
                <tr>
                    <th>Date Incurred</th>
                    <th>Amount</th>
                    <th>Payee</th>
                    <th>Deducted From</th>
                    <th class="mvdr hddr">Mv</th>
                </tr>
            </thead>
            <tbody>
            <?php for ($j=0; $j<count($amt); $j++) : ?>
                <tr>
                    <td class="col1"><?= $dte[$j];?></td>
                    <td class="col2"><?= $amt[$j];?></td>
                    <td class="col3"><?= $pye[$j];?></td>
                    <td class="col4"><?= $ded[$j];?></td>
                    <td class="mvdr"><input id="dr<?= $eid[$j];?>"
                            type="checkbox" /></td>
                </tr>
            <?php endfor; ?>
            </tbody>
        </table>
        <hr />
        <?php for ($i=0; $i<count($dr); $i++) : ?>
            <span class="SmallHeading">For debit card <em style="color:brown;">
            <?= $dr[$i];?></em>:</span>&nbsp;&nbsp;&nbsp;
            <span class="mvtodr"><strong class="disptodr">To:</strong> 
            <input id="todr<?= $i;?>" type="checkbox" /></span><br />
            <table class="drtbl">
                <thead>
                    <tr>
                        <th>Date Incurred</th>
                        <th>Amount</th>
                        <th>Payee</th>
                        <th>Deducted From</th>
                        <th class="mvdr hddr">Mv</th>
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
                        <td class="mvdr"><input id="dr<?= $did[$j];?>"
                            type="checkbox" /></td>
                    </tr>
                    <?php endif; ?>
                <?php endfor; ?>
                </tbody>
            </table>
            <hr />
        <?php endfor; ?>
    </div>
</div>
<p style="clear:left;margin-left:16px;">
    <a href="http://validator.w3.org/check?uri=referer">
        <img src="http://www.w3.org/Icons/valid-xhtml10"
        alt="Valid XHTML 1.0 Strict" height="31" width="88" />
    </a>
</p>

<script src="../scripts/jquery-1.12.1.js" type="text/javascript"></script>
<script src="../scripts/manageExp.js" type="text/javascript"></script>

</body>
</html>
