<?php
/**
 * This is an effort to replace the broken and impossible to maintain (thanks
 * to Microsoft) 'budget.xlsm'. The latest insult is that Excel 2016 has no
 * Userform support!!!
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license
 */
require "budget_setup.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Budget Tracker</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Rolling 3-month budget tracker" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <link href="standards.css" type="text/css" rel="stylesheet" />
    <link href="budget.css" type="text/css" rel="stylesheet" />
    <link href="charges.css" type="text/css" rel="stylesheet" />
    <link href="modals.css" type="text/css" rel="stylesheet" />
    <script type="text/javascript">
        var setup;
    </script>
</head>

<body>
    <div id="newbud"><?= $new_budget;?></div>
    <div id="chgaccts"><?= $cdcards;?></div>
    <div id="budget">
        <table id="roll3">
            <colgroup>
                <col style="width:200px" />
                <col style="width:108px" />
                <col style="width:140px" />
                <col style="width:140px" />
                <col style="width:140px" />
                <col style="width:90px" />
                <col style="width:64px" />
            </colgroup>
            <thead>
                <tr>
                    <th>Account Name</th>
                    <th class="heavy-right">Budget</th>
                    <th><?= $month[0];?></th>
                    <th><?= $month[1];?></th>
                    <th><?= $month[2];?></th>
                    <th>AutoPay</th>
                    <th>Day</th>
                </tr>
            </thead>
            <tbody>
                <?php for($j=1; $j<count($lines)-6; $j++) : ?>
                <tr>
                    <td class="acct"><?= $lines[$j][0];?></td>
                    <td class="amt"><?= $lines[$j][1];?></td>
                    <td class="mo1"><?= $lines[$j][2];?></td>
                    <td class="mo2"><?= $lines[$j][3];?></td>
                    <td class="mo3"><?= $lines[$j][4];?></td>
                    <td class="ap"><?= $lines[$j][5];?></td>
                    <td class="apday"><?= $lines[$j][6];?></td>
                </tr>
                <?php endfor; ?>
                <tr>
                    <td class="gray-title"
                        style="text-align:center;">Temporary Accounts</td>
                    <td class="gray-title"></td>
                    <td class="gray-title"></td>
                    <td class="gray-title"></td>
                    <td class="gray-title"></td>
                    <td class="gray-title"></td>
                    <td class="gray-title"></td>
                </tr>
                <?php for ($k=count($lines)-5; $k<count($lines); $k++) : ?>
                <tr>
                    <td class="acct"><?= $lines[$k][0];?></td>
                    <td class="amt"><?= $lines[$k][1];?></td>
                    <td class="mo0"><?= $lines[$k][2];?></td>
                    <td class="mo1"><?= $lines[$k][3];?></td>
                    <td class="mo2"><?= $lines[$k][4];?></td>
                    <td class="ap"><?= $lines[$k][5];?></td>
                    <td class="apday"><?= $lines[$k][6];?></td>
                </tr>
                <?php endfor; ?>
                <tr id="balances">
                    <td class="BoldText heavy-top">Checkbook Balance</td>
                    <td class="balance heavy-top"><?= $bbal;?></td>
                    <td class="balance heavy-top"><?= $bal1;?></td>
                    <td class="balance heavy-top"><?= $bal2;?></td>
                    <td class="balance heavy-top"><?= $bal3;?></td>
                    <td class="gray-title heavy-top"></td>
                    <td class="gray-title heavy-top"></td>
                </tr>
                <tr>
                    <td class="gray-title">Credit Cards</td>
                    <td colspan="6" class="gray-title" style="text-align:left;">
                        &nbsp;&nbsp;-- Not deducted until reconciled --</td>
                </tr>
                <?php for($l=0; $l<$crcards; $l++) : ?>
                <tr>
                    <td class="cname"><?= $card_names[$l];?></td>
                    <td class="camt"><?= $crbalances[$l];?></td>
                    <td colspan="5" class="gray-title"></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
        <div id="bttns">
            <button id="expense">Make Payment</button><br />
            <button id="deposit">Make Deposit</button><br />
            <button id="income">Enter Income</button><br />
            <button id="movefunds">Move Funds</button><br />
            Account Management Tools:<br />
            <select id="mgmt">
                <option value="none">Select From List:</option>
                <option value="apsetup">Setup AutoPay</option>
                <option value="cd_cards">Setup/Change Debit/Credit Info</option>
                <option value="renameacct">Rename Account</option>
                <option value="addacct">Add Account</option>
                <option value="delacct">Delete Account</option>
                <option value="mvacct">Move Account</option>
            </select>
        </div>
    </div>

    <!-- This div holds all the modal formas -->
    <div id="allForms">
        <div id="box" style="display:none">
            <span id="modal_accts">Use account: </span>
            <span id="modal_cards">Charge to: </span>
            <select id="cc">
                <option value="none">Account</option>
                <option value="card1">Visa</option>
                <option value="card2">Citi</option>
                <option value="db1">Wells Fargo</option>
            </select><br />
            Enter the amount of the expense:<br />
            <input type="text" id="expamt"  /><br /><br />
            <button id="pay">Pay</button>
        </div>
    </div>
    <!-- after validation, place accordingly:
    <p style="clear:left;">
        <a href="http://validator.w3.org/check?uri=referer">
            <img src="http://www.w3.org/Icons/valid-xhtml10"
            alt="Valid XHTML 1.0 Strict" height="31" width="88" />
        </a>
    </p>
    -->
    <script src="jquery-1.12.1.js" type="text/javascript"></script>
    <script src="budget.js" type="text/javascript"></script>
    <script src="modals.js" type="text/javascript"></script>
    <script src="jQnumberFormat.js" type="text/javascript"></script>
</body>
</html>