<?php
/**
 * This is an effort to replace the broken and impossible to maintain (thanks
 * to Microsoft) 'budget.xlsm'. The latest insult is that MacOS Excel 2016 has
 * no Userform support!! This program allows a user to create, setup, and manage
 * his/her own personal budget online. Management tools are presented on the home 
 * page ('main/budget.php').
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
    <link href="../styles/jquery-ui.css" type="text/css" rel="stylesheet" />
    <link href="../styles/standards.css" type="text/css" rel="stylesheet" />
    <link href="../styles/panel.css" type="text/css" rel="stylesheet" />
    <link href="../styles/budget.css" type="text/css" rel="stylesheet" />
    <link href="../styles/charges.css" type="text/css" rel="stylesheet" />
    <link href="../styles/modals.css" type="text/css" rel="stylesheet" />
    <script src="../scripts/jquery-1.12.1.js" type="text/javascript"></script>
    <script src="../scripts/jquery-ui.js" type="text/javascript"></script>
    <script type="text/javascript">
        var setup;
    </script>
</head>

<body>
    <?php require "panel.php"; ?>

    <div id="chgaccts"><?= $cdcards;?></div>
    <div id="budget">
        <table id="roll3">
            <colgroup>
                <col style="width:200px" />
                <col style="width:86px" />
                <col style="width:100px" />
                <col style="width:100px" />
                <col style="width:100px" />
                <col style="width:10s0px" />
                <col style="width:64px" />
                <col style="width:10px" class="noshow" />
                <col style="width:16px" class="noshow" />
            </colgroup>
            <thead>
                <tr>
                    <th>Budget Acct Name</th>
                    <th class="heavy-right">Budget</th>
                    <th><?= $month[0];?></th>
                    <th><?= $month[1];?></th>
                    <th><?= $month[2];?></th>
                    <th>AutoPay</th>
                    <th>Day</th>
                    <th class="noshow">Paid</th>
                    <th class="noshow">Income</th>
                </tr>
            </thead>
            <tbody>
                <!-- all but temporary accounts -->
                <?php for($j=0; $j<count($lines)-6; $j++) : ?>
                <tr>
                    <td class="acct"><?= $lines[$j][0];?></td>
                    <td class="amt"><?= $lines[$j][1];?></td>
                    <td class="mo1"><?= $lines[$j][2];?></td>
                    <td class="mo2"><?= $lines[$j][3];?></td>
                    <td class="mo3"><?= $lines[$j][4];?></td>
                    <td class="ap"><?= $lines[$j][5];?></td>
                    <td class="apday"><?= $lines[$j][6];?></td>
                    <td class="noshow"><?= $lines[$j][7];?></td>
                    <td class="noshow"><?= $lines[$j][8];?></td>
                </tr>
                <?php endfor; ?>
                <tr>
                    <td class="acct gray-title"
                        style="text-align:center;">Temporary Accounts</td>
                    <td class="amt gray-title"></td>
                    <td class="mo1 gray-title"></td>
                    <td class="mo2 gray-title"></td>
                    <td class="mo3 gray-title"></td>
                    <td class="ap gray-title"></td>
                    <td class="apday gray-title"></td>
                    <td class="noshow"></td>
                    <td class="noshow"></td>
                </tr>
                <!-- temporary accounts -->
                <?php for ($k=count($lines)-5; $k<count($lines); $k++) : ?>
                <tr>
                    <td class="acct"><?= $lines[$k][0];?></td>
                    <td class="amt"><?= $lines[$k][1];?></td>
                    <td class="mo0"><?= $lines[$k][2];?></td>
                    <td class="mo1"><?= $lines[$k][3];?></td>
                    <td class="mo2"><?= $lines[$k][4];?></td>
                    <td class="ap"><?= $lines[$k][5];?></td>
                    <td class="apday"><?= $lines[$k][6];?></td>
                    <td class="noshow"><?= $lines[$k][7];?></td>
                    <td class="noshow"><?= $lines[$k][8];?></td>
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
                    <td class="noshow"></td>
                    <td class="noshow"></td>
                </tr>
                <tr>
                    <td class="gray-title">Credit Cards</td>
                    <td colspan="6" class="gray-title" style="text-align:left;">
                        &nbsp;&nbsp;-- Not deducted until reconciled --</td>
                </tr>
                <?php for($l=0; $l<$card_cnt; $l++) : ?>
                <tr>
                    <td class="cname"><?= $cards[$l];?></td>
                    <td></td>
                    <td id="past0" class="camt"><?= $old_card_balances[$l][0];?></td>
                    <td id="past1" class="camt"><?= $old_card_balances[$l][1];?></td>
                    <td class="camt"><?= $crbalances[$l];?></td>
                    <td colspan="3" class="gray-title"></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>
    <!-- This div holds all the modal formas -->
    <div id="allForms">
        <!-- autopay -->
        <div id="ap">
           <p id="inst">The following items are due for auto payment. Select an item
           to pay, and/or when done, select 'Finished' [Note: this box moveable]</p>
           <table id="modal_table">
               <tbody></tbody>
           </table>
        </div>
        <!-- pay expense -->
        <div id="box">
            <span id="modal_accts">Deduct from: </span>
            <span id="modal_cards">Pay with: </span>
            <select id="cc">
                <option value="none">Check/Draft</option>
                <?php for ($y=0; $y<count($cards); $y++) : ?>
                <option value="card<?= $y+1;?>"><?= $cards[$y];?></option>
                <?php endfor; ?>
                <?php for ($z=0; $z<count($debits); $z++) : ?>
                <option value="debit<?= $z+1;?>"><?= $debits[$z];?></option>
                <?php endfor; ?>
            </select><br />
            Enter the amount of the expense:<br />
            $ <input type="text" id="expamt"  /><br />
            Paid to: <input type="text" id="payee" /><br />
            <button id="pay">Pay</button>
        </div>
        <!-- monthly income -->
        <div id="distinc">
            Please enter the amount of monthly income you wish to distribute:
            Note that income will be added to accounts not already having
            received the full budgeted amount, and will stop when the funds
            run out. Any overages will be placed in 'Undistributed Funds',
            which can later be moved to other accounts ('Move Funds' button).
            <br /><br />
            Enter income: $ <input id="incamt" type="text" /><br /><br />
            <button id="dist">Distribute</button>
        </div>
        <!-- rename account -->
        <div id="rename">
            <span id="asel">Select the account you wish to rename: </span>
            Supply the new name: <input id="newname" type="text" /><br />
            <button id="doit">Change Name</button>
        </div>
        <!-- add account -->
        <div id="addacct">
            Enter the account information for the new addition to the budget:
            <br /><br />New Account Name:<br />
            <input id="newacct" type="text" /><br /><br />
            Enter the monthly amount to be budgeted to this account:<br />
            $ <input id="mo" type="text" /><br /><br />
            Use account management tools to modify other features once created (e.g.
            'Change Autopay', 'Move Funds', etc.)<br /><br />
            <button id="addit">Add Account</button>
        </div>
        <!-- one-time deposit -->
        <div id="dep">
            Enter the amount to be deposited (it will be placed in 'Undistributed
            Funds')<br />
            $ <input type="text" id="depo" /><br /><br />
            <button id="depfunds">Deposit Funds</option>
        </div>
        <!-- transfer funds -->
        <div id="xfr">
            Transfer the following amount:<br />$ <input type="text" 
                id="xframt" /><br />
            <span id="xfrfrom">Take from:</span><br />
            <span id="xfrto">Place in: </span>
            <button id="transfer">Transfer</button>
        </div>
        <!-- move account -->
        <div id="mv">
            Note: you cannot move the 'Undistributed Funds' nor any 'Temporary
            Account'<br /><br />
            <span id="mvfrom">Place the following account: <br /></span><br />
            <span id="mvto">Directly above: <br /></span><br /><br />
            <button id="mvit">Move</button>
        </div>
        <!-- move account -->
        <div id="del">
            Note: you can only delete accounts you create (e.g. not 'Undistributed
            Funds' nor 'Temporary Accounts')<br /><br />
            <span id="delacct">Delete: </span><br /><br />
            <button id="delit">Delete Account</button>
        </div>
        <!-- add new Cr/Dr card -->
        <div id="cdadd">
            Enter a unique name:<br />
            <input id="cda" type="text" /><br />
            Select the type of card:<br />
            <select id="cdprops">
                <option value="Credit">Credit</option>
                <option value="Debit">Debit</option>
            </select><br />
            <button id="newcd">Submit</button>
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
    <script src="../scripts/budget.js" type="text/javascript"></script>
    <script src="../scripts/modals.js" type="text/javascript"></script>
    <script src="../scripts/panel.js" type="text/javascript"></script>
    <script src="../scripts/jQnumberFormat.js" type="text/javascript"></script>
</body>
</html>