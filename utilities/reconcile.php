<?php
/**
 * This module allows the user to select a credit card to be reconciled against
 * the corresponding monthly statement. 
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "selectCrCards.php";
require "getCrData.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Reconcile Monthly Statement</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Credit card statement reconciliation" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <link href="../styles/standards.css" type="text/css" rel="stylesheet" />
    <link href="../styles/charges.css" type="text/css" rel="stylesheet" />
    <link href="../styles/reconcile.css" type="text/css" rel="stylesheet" />
</head>

<body>

<div style="margin-left:16px;" id="container">
    <p class="NormalHeading">This form will allow you to select from your current
        credit cards and reconcile that card against your monthly statement.
        Your budget will be automatically updated to show payment of those
        charges.</p>
    <button id="rtb">Return to Budget</button>

    <form id="form" method="POST" action="saveReconciledCharges.php">
    <p class="NormalHeading">Select the card to be reconciled: <?= $selectHtml;?>
        &nbsp;&nbsp;&nbsp;<button id="reconcile">Reconcile</button></p>
    <div id="statement">
    <?php for ($i=0; $i<$card_cnt; $i++) : ?>
        <?php
        $cardlist = 'card' . $i;
        switch($i) {
        case 0:
            $card_data = $credit_charges['card1'];
            break;
        case 1:
            $card_data = $credit_charges['card2'];
            break;
        case 2:
            $card_data = $credit_charges['card3'];
            break;
        case 3:
            $card_data = $credit_charges['card4'];
            break;
        }
        ?>
        <div id="list<?= $i;?>">
            <table>
                <thead>
                    <tr>
                        <th>Charged To:</th>
                        <th>Date:</th>
                        <th>Payee:</th>
                        <th>Amount</th>
                        <th>Pay</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for($j=0; $j<count($card_data); $j++) : ?>
                    <tr>
                        <td class="left chgto"><?= $card_data[$j][0];?></td>
                        <td class="chgdate"><?= $card_data[$j][1];?></td>
                        <td class="left chgpayee"><?= $card_data[$j][2];?></td>
                        <td class="right chgamt"><?= $card_data[$j][3];?></td>
                        <td><input type="checkbox" name="del[]"
                            id="<?= $cardlist;?>rec<?= $j;?>"
                            value="<?= $cardlist;?>rec<?= $j;?>" /></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table><br />
        </div>
    <?php endfor; ?>
    </div>
    </form>
</div>

<script src="../scripts/jquery-1.12.1.js" type="text/javascript"></script>
<script src="../scripts/reconcile.js" type="text/javascript"></script>
</body>

</html>
