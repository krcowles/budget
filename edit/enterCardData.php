<?php
/**
 * This file allows the user to add charges to any credit cards currently in place
 * PHP Version 7.1
 * 
 * @package BUDGET
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../utilities/getCrData.php";
require "../utilities/selectCrCards.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Credit/Debit Card Setup</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Rolling 4-month budget tracker" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <link href="../styles/standards.css" type="text/css" rel="stylesheet" />
    <link href="../styles/charges.css" type="text/css" rel="stylesheet" />
    <style type="text/css">
        #return { margin-left: 40px; }
        #existing { display: none }
        .left { text-align: left }
        .right { text-align: right }
        #item_modal { display: none; }
        .itmname { width: 140px; }
        .modwidth { width: 200px; }
        .modin { width: 190px; }
    </style>
</head>

<body>
<div id="main">
    <p class="NormalHeading">You may use this form to enter current charges against 
        the credit cards now in place, or to edit existing charges. The admin can
        also assist with transferring charges if currently stored on a 
        spreadsheet.</p>
    <p id="olddat" style="display:none;"><?= $card_cnt;?></p>
    <button id="save">Save All Changes</button>
    <button id="return">Return to Budget</button><br /><br />
    <div id="existing">
    <?php for ($i=0; $i<$card_cnt; $i++) : ?>
        <?php
        $ed = 'ed' . $i . 'item';
        $card_name = $cards[$i];
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
        <span class="BoldText">These are your current charges against
            <?= $card_name;?>
        </span>
        <table>
            <thead>
                <tr>
                    <th>Charged Against:</th>
                    <th>Date:</th>
                    <th>Payee:</th>
                    <th>Amount</th>
                    <th>Edit</th>
                </tr>
            </thead>
            <tbody>
                <?php for($j=0; $j<count($card_data); $j++) : ?>
                <tr>
                    <td class="left chgto"><?= $card_data[$j][0];?></td>
                    <td class="chgdate"><?= $card_data[$j][1];?></td>
                    <td class="left chgpayee"><?= $card_data[$j][2];?></td>
                    <td class="right chgamt"><?= $card_data[$j][3];?></td>
                    <td><input type="checkbox"
                        id="<?= $ed;?><?= $j;?>" /></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table><br />
    <?php endfor; ?>
    </div>
    <form id="form" action="saveNewCharges.php" method="POST">
        <div id="new">
            <span class="BoldText">Enter New Charges Here (Up to 5 per Save)<br />
            For</span> <?= $selectHtml;?><br />
            <table>
                <thead>
                    <tr>
                        <th>Charged Against:</th>
                        <th>Date:</th>
                        <th>Payee:</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($k=0; $k<5; $k++) : ?>
                    <tr>
                        <td><input type="text" name="newcharge[]" /></td>
                        <td><input type="text" name="newdate[]" /></td>
                        <td><input type="text" name="newpayee[]" /></td>
                        <td><input type="text" name="newamt[]" /></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table><br /><br />
        </div>
    </form>
</div>

<script src="../scripts/jquery-1.12.1.js" type="text/javascript"></script>
<script src="../scripts/modals.js" type="text/javascript"></script>
<script src="../scripts/enterCardData.js" type="text/javascript"></script>

<div id="item_modal">
    You may edit the content for the following item. When done editing,
    click on "Save", or "Cancel" if no changes are desired.<br />
    <button id="svmodal" style="margin-top:8px;">Save</button>
    <table>
        <tbody>
            <tr>
                <td class="left itmname" >Charge to:</td>
                <td class="modwidth"><input id="chg" class="modin"
                    type="text" value="" /></td>
            </tr>
            <tr>
                <td class="left itmname">Date Entered:</td>
                <td class="modwidth"><input id="de" class="modin"
                    type="text" /></td>
            </tr>
            <tr>
                <td class="left itmname">Payee:</td>
                <td class="modwidth"><input id="pay" class="modin"
                    type="text" /></td>
            </tr>
            <tr>
                <td class="left itmname">Amount:</td>
                <td class="modwidth"><input id="namt" class="modin"
                    type="text" /></td>
            </tr>
        </tbody>
    </table>
</div>

</body>

</html>