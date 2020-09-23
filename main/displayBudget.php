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
session_start();

if (isset($_SESSION['userid'])) {
    include "budgetSetup.php";
} else {
    die("There has been no legitimate login");
}
$admin = $_SESSION['userid'] == '4' ? 'yes' : 'no'
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
    <link href="../styles/standards.css" type="text/css" rel="stylesheet" />
    <link href="../styles/menu.css" type="text/css" rel="stylesheet" />
    <link href="../styles/budget.css" type="text/css" rel="stylesheet" />
    <link href="../styles/charges.css" type="text/css" rel="stylesheet" />
    <link href="../styles/modals.css" type="text/css" rel="stylesheet" />
</head>

<body>
    <?php require "menu.html"; ?>
    <p id="mstr" style="display:none;"><?=$admin;?></p>
    <pre><button id="admin">Admin</button></pre>
    <p id="usercookies" style="display:none"><?=$menu_item?></p>
    <div id="budget">
        <table id="roll3">
            <colgroup>
                <col style="width:260px" />
                <col style="width:100px" />
                <col style="width:120px" />
                <col style="width:120px" />
                <col style="width:120px" />
                <col style="width:100px" />
                <col style="width:64px" />
                <col style="width:10px" class="noshow" />
                <col style="width:16px" class="noshow" />
            </colgroup>
            <thead>
                <tr>
                    <th>Budget Acct Name</th>
                    <th class="heavy-right">Monthly Budget</th>
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
                <?php for($j=0; $j<count($account_names); $j++) : ?>
                    <?php if ($j === $user_cnt + 1) : ?>
                    <tr id="tmphd">
                        <td class="BoldText grayCell"
                            style="text-align:center;">Temporary Accounts</td>
                        <td class="amt grayCell"></td>
                        <td class="mo1 grayCell"></td>
                        <td class="mo2 grayCell"></td>
                        <td class="mo3 grayCell"></td>
                        <td class="ap grayCell"></td>
                        <td class="apday grayCell"></td>
                        <td class="noshow"></td>
                        <td class="noshow"></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td class="acct"><?= $account_names[$j];?></td>
                        <td class="amt"><?= $budgets[$j];?></td>
                        <td class="mo1"><?= $prev0[$j];?></td>
                        <td class="mo2"><?= $prev1[$j];?></td>
                        <td class="mo3"><?= $current[$j];?></td>
                        <td class="ap apcolor"><?= $autopay[$j];?></td>
                        <?php if ($day[$j] == 0) : ?>
                            <td class="apcolor apday"></td>
                        <?php else : ?>
                            <td class="apcolor apday"><?= $day[$j];?></td>
                        <?php endif; ?>
                        <td class="noshow"><?= $paid[$j];?></td>
                        <td class="noshow"><?= $income[$j];?></td>
                    </tr>
                <?php endfor; ?>
                <tr id="cchd">
                    <td class="BoldText grayCell">Credit Cards</td>
                    <td colspan="6" class="grayCell" style="text-align:left;">
                        &nbsp;&nbsp;-- Not deducted from Balance until
                        reconciled --</td>
                </tr>
                <?php for ($cc=0; $cc<count($cr); $cc++) : ?>
                    <tr>
                        <td class="acct"><?= $cr[$cc];?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><?= $cardbal[$cc]['bal'];?></td>
                        <td colspan="2"></td>
                    </tr>
                <?php endfor; ?>
                <tr id="balances">
                    <td class="BoldText heavyTop grayCell">Checkbook Balance</td>
                    <td class="balance heavyTop"><?= $balBudget;?></td>
                    <td class="balance heavyTop"><?= $balPrev0;?></td>
                    <td class="balance heavyTop"><?= $balPrev1;?></td>
                    <td class="balance heavyTop"><?= $balCurrent;?></td>
                    <td class="grayCell heavyTop" colspan="2"></td>
                    <td class="noshow grayCell"></td>
                    <td class="noshow grayCell"></td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- This div holds all the modal formas -->
    <div id="allForms">
        <!-- autopay -->
        <div id="ap">
           <p id="inst">The following items are due for auto payment. Select an item
           to pay, and/or when done, select 'Finished'</p>
        </div>
        <!-- pay expense -->
        <div id="box">
            Deduct from:
            <div id="fsel0"><?= $fullsel;?></div>
            Pay with:
            <div id="csel0"><?= $allCardsHtml;?></div>
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
        <!-- one-time deposit -->
        <div id="dep">
            Enter the amount to be deposited (it will be placed in 'Undistributed
            Funds')<br />
            $ <input type="text" id="depo" /><br /><br />
            <button id="depfunds">Deposit Funds</button>
        </div>
        <!-- transfer funds -->
        <div id="xfr">
            Transfer the following amount:<br />$ <input type="text" 
                id="xframt" /><br />
            Take from:
            <div id="xfrfrom"><?= $fullsel;?></div>
            Place in:<br />
            <div id="xfrto"><?= $fullsel;?></div><br />
            <button id="transfer">Transfer</button>
        </div>
        <!-- reconcile credit card statement -->
        <div id="reconcile">
            Please select the card you wish to reconcile:<br />
            <div id="ccsel0"><?= $ccHtml;?></div><br />
            <button id="usecard">Reconcile</button>
        </div>
        <!-- schedule an autopay -->
        <div id="auto">
            Prompt for automatic payment of:<br />
            <div id="apsel"><?= $fullsel;?></div>
            Method to be applied:<br />
            <div id="ccselap"><?= $allCardsHtml;?> on day
            <input id="useday" type="text" /><br />
            of each month.</div>
            <button id="perfauto">Set up</button>
        </div>
        <!-- delete autopay -->
        <div id="delauto">
            Select the account for which you wish to delete prompted autopay:<br />
            <div id="delapacct"><?= $fullsel;?></div><br />
            <button id="remap">Delete Autopay</button>
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
        <!-- delete Cr/Dr card -->
        <div id="delcrdr">
            Select the card you wish to delete:<br />
            <div id="deletecard"><?= $allCardsHtml;?></div><br />
            <button id="godel">Delete Card</button>
        </div>
        <!-- add account -->
        <div id="addacct">
            Enter the account information for the new addition to the budget:
            <br /><br />New Account Name:<br />
            <input id="newacct" type="text" /><br /><br />
            Enter the monthly amount to be budgeted to this account:<br />
            $ <input id="mo" type="text" /><br />(Use 'Transfer Funds' to establish
            this month's balance, or other tools to modify
            other features.<br /><br />
            <button id="addit">Add Account</button>
        </div>
        <!-- delete account -->
        <div id="delexisting">
            Note: you can only delete accounts you create (i.e. not 'Undistributed
            Funds' or 'Temporary Accounts')
            <span style="color:brown;">NOTE: Please Ensure the budget-to-delete
            has a balance of $0.</span><br /><br />
            <div id="remacct">Delete: <?= $partsel;?></div><br />
            <button id="delit">Delete Account</button>
        </div>
        <!-- move account -->
        <div id="mv">
            Note: you cannot move the 'Undistributed Funds' nor any 'Temporary
            Account'<br /><br />
            <div id="mvfrom">Place the following account: <br />
            <?= $partsel;?></div>
            <div id="mvto">Directly above: <br />
            <?= $partsel;?></div><br />
            <button id="mvit">Move</button>
        </div>
        <!-- rename account -->
        <div id="rename">
            <span id="asel">Select the account you wish to rename: 
            <?= $fullsel;?></span><br /><br />
            Supply the new name: <input id="newname" type="text" /><br /><br />
            <button id="ren">Change Name</button>
        </div>
        <!-- monthly report -->
        <div id="morpt">
            Please select the report month:<br />
            <select id="rptmo" name="month">
                <option value="January">January</option>
                <option value="February">February</option>
                <option value="March">March</option>
                <option value="April">April</option>
                <option value="May">May</option>
                <option value="June">June</option>
                <option value="July">July</option>
                <option value="August">August</option>
                <option value="September">September</option>
                <option value="October">October</option>
                <option value="November">November</option>
                <option value="December">December</option>
            </select><br /><br />
            <button id="genmo">Generate</button>
        </div>
        <div id="yearrpt">
                Please select the report year<br />
                <select id="rptyr" name="year">
                    <option value="<?=$thisyear;?>"><?=$thisyear;?></option>
                    <option value="<?=$prioryr1;?>"><?=$prioryr1;?></option>
                    <option value="<?=$prioryr2;?>"><?=$prioryr2;?></option>
                </select><br /><br />
                <button id="genyr">Generate</button>
        </div>
    </div>
    <div id="preloader">
        <img src="../images/preload.gif" alt="waiting..." />
    </div>
    <p style="clear:left;margin-left:16px;">
        <a href="http://validator.w3.org/check?uri=referer">
            <img src="http://www.w3.org/Icons/valid-xhtml10"
            alt="Valid XHTML 1.0 Strict" height="31" width="88" />
        </a>
    </p>
    <script src="../scripts/jquery-1.12.1.js" type="text/javascript"></script>
    <script src="../scripts/menu.js" type="text/javascript"></script>
    <script src="../scripts/budget.js" type="text/javascript"></script>
    <script src="../scripts/modals.js" type="text/javascript"></script>
    <script src="../scripts/jQnumberFormat.js" type="text/javascript"></script>
</body>
</html>