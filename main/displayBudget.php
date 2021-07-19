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
    die("Your session has expired");
}
/**
 * NOTE: When working on other sites, the userid for admin may be different and
 * still active! In that case, log out and re-log in (admin/logout.php)
 */
$admin = $_SESSION['userid'] == '4' ? 'yes' : 'no'
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Budget Tracker</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Rolling 3-month budget tracker" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="../styles/bootstrap.min.css" type="text/css" rel="stylesheet" />
    <link href="../styles/charges.css" type="text/css" rel="stylesheet" />
    <link href="../styles/budget.css" type="text/css" rel="stylesheet" />
    <link href="../styles/modals.css" type="text/css" rel="stylesheet" />
</head>

<body>
<?php require "navbar.php"; ?>
<p id="mstr" style="display:none;"><?=$admin;?></p>
<pre><button id="admin">Admin</button></pre>
<p id="usercookies" style="display:none"><?=$menu_item?></p>
<!-- for deferred income -->
<p id="currmo" style="display:none;"><?=$current_month;?></p>
<p id="nextmo" style="display:none;"><?=$next_month;?></p>
<p id="deferral" style="display:none;"><?=$trigger_deferral;?></p>
<p id="defamt" style="display:none;"><?=$deferred_amount;?></p>

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
                <td class="noshow"></td>
                <td class="noshow"></td>
            </tr>
            <?php for ($cc=0; $cc<count($cr); $cc++) : ?>
                <tr>
                    <td class="acct"><?= $cr[$cc];?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><?= $cardbal[$cc]['bal'];?></td>
                    <td colspan="2"></td>
                    <td class="noshow"></td>
                    <td class="noshow"></td>
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

<?php require "bootstrapModals.html"; ?>

<div id="preloader">
    <img src="../images/preload.gif" alt="waiting..." />
</div>
<br /><br />

<script src="https://unpkg.com/@popperjs/core@2.4/dist/umd/popper.min.js"></script>
<script src="../scripts/bootstrap.min.js"></script>
<script src="../scripts/jquery-1.12.1.js"></script>
<script src="../scripts/menus.js"></script>
<script src="../scripts/budget.js"></script>
<script src="../scripts/jQnumberFormat.js"></script>
</body>

</html>