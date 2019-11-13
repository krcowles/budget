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
require "../utilities/getBudgetData.php";

$acct_sel = '<select class="acctsel" name="acctsel[]">';
$acct_sel .= '<option value="" selected>Unspecified</option>';
for ($j=0; $j<count($account_names); $j++) {
    $acct_sel .= '<option value="' . $account_names[$j] . '">' .
        $account_names[$j] . '</option>';
}
$acct_sel .= '</select>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Edit Credit Card Charges</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Rolling 4-month budget tracker" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <link href="../styles/standards.css" type="text/css" rel="stylesheet" />
    <link href="../styles/charges.css" type="text/css" rel="stylesheet" />
    <link href="../styles/modals.css" type="text/css" rel="stylesheet" />
    <style type="text/css">
        #return { margin-left: 40px; }
        #existing { display: none }
        .left { text-align: left }
        .right { text-align: right }
        .itmname { width: 140px; vertical-align:middle; }
        .modwidth { width: 200px; }
        .modin { width: 190px; }
        #modalforms { display: none; }
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
                    <!-- NOTE: no linefeed before $card_data, lest js read it! -->
                    <td><span id="oc<?= $i;?>item<?= $j;?>"
                        style="display:none;"><?= $card_data[$j][0];?></span>
                        <?= $acct_sel;?>
                    </td>
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
            <span class="NormalHeading">Enter New Charges Here (Up to 5 per Save)
            For</span> <?= $selectHtml;?>
                <span class="NormalHeading">Card Only</span><br />
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
                        <td><?= $acct_sel;?></td>
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
<div id="modalforms">
    <div id="item_edit">
        You may edit the content for the following item. When done editing,
        click on "Save", or "Cancel" if no changes are desired.<br />
        <button id="svmodal" style="margin-top:8px;">Save</button>
        <table id="modtbl">
            <tbody>
                <tr>
                    <td class="left itmname" ><span class="mod">Deduct 
                        From:</span></td>
                    <td><?= $acct_sel;?></td>
                </tr>
                <tr>
                    <td class="left itmname"><span 
                        class="mod">Date Entered:</span></td>
                    <td class="modwidth"><input id="de" class="modin"
                        type="text" /></td>
                </tr>
                <tr>
                    <td class="left itmname"><span class="mod">Payee:</span></td>
                    <td class="modwidth"><input id="pay" class="modin"
                        type="text" /></td>
                </tr>
                <tr>
                    <td class="left itmname"><span class="mod">Amount:</span></td>
                    <td class="modwidth"><input id="namt" class="modin"
                        type="text" /></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script src="../scripts/jquery-1.12.1.js" type="text/javascript"></script>
<script src="../scripts/modals.js" type="text/javascript"></script>
<script src="../scripts/editCreditCharges.js" type="text/javascript"></script>

</body>

</html>