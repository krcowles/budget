<?php
/**
 * This program allows a user to create, setup, and manage his/her own
 * personal budget online. Management tools are presented on the home 
 * page ('main/budget.php').
 * PHP Version 7.3
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
    <title>Personal Budget</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Personal Budget management" />
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
<p id="mstr" class="noshow"><?=$admin;?></p>
<div id="ubtns">
<button id="medpg" type="button" class="btn btn-secondary btn-sm">
    Medical Refs</button>
<button id="admin" type="button" class="btn btn-secondary btn-sm">Admin</button>
</div>
<p id="usercookies" class="noshow"><?=$menu_item?></p>
<!-- for deferred income -->
<p id="currmo" class="noshow"><?=$current_month;?></p>
<p id="nextmo" class="noshow"><?=$next_month;?></p>
<p id="deferral" class="noshow"><?=$trigger_deferral;?></p>
<p id="defamt" class="noshow"><?=$deferred_amount;?></p>

<?php if ($nonmonthly) : ?>
<p id="combo_acct" class="noshow"><?=$nmfbal;?></p> 
<p id="expected_sum" class="noshow"><?=$nmebal;?></p>                 
<?php endif; ?>

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
                <th class="tableHdrColor">Budget Acct Name</th>
                <th class="tableHdrColor heavy-right">Monthly Budget</th>
                <th class="tableHdrColor"><?= $month[0];?></th>
                <th class="tableHdrColor"><?= $month[1];?></th>
                <th class="tableHdrColor"><?= $month[2];?></th>
                <th class="tableHdrColor">AutoPay With</th>
                <th class="tableHdrColor">Day of Month</th>
                <th class="noshow">Paid</th>
                <th class="noshow">Income</th>
            </tr>
        </thead>
        <tbody>
            <?php for($j=0; $j<count($account_names); $j++) : ?>
                <?php if ($j === $user_cnt + 1) : ?>
                <tr id="tmphd">
                    <td class="BoldText tmp_color"
                        style="text-align:center;">Temporary Accounts</td>
                    <td class="amt tmp_color"></td>
                    <td class="mo1 tmp_color"></td>
                    <td class="mo2 tmp_color"></td>
                    <td class="mo3 tmp_color"></td>
                    <td class="ap tmp_color"></td>
                    <td class="apday tmp_color"></td>
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
                <td class="BoldText cc_color">Credit Cards</td>
                <td colspan="6" class="cc_color" style="text-align:left;">
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
                <td class="BoldText heavyTop">Checkbook Balance</td>
                <td class="balance heavyTop"><?= $balBudget;?></td>
                <td class="balance heavyTop"><?= $balPrev0;?></td>
                <td class="balance heavyTop"><?= $balPrev1;?></td>
                <td class="balance heavyTop"><?= $balCurrent;?></td>
                <td class="heavyTop" colspan="2"></td>
                <td class="noshow heavyTop"></td>
                <td class="noshow heavyTop"></td>
            </tr>
        </tbody>
    </table>
</div>

<?php require "bootstrapModals.html"; ?>
<br /><br />

<script src="https://unpkg.com/@popperjs/core@2.4/dist/umd/popper.min.js"></script>
<script src="../scripts/bootstrap.min.js"></script>
<script src="../scripts/jquery.min.js"></script>
<script src="../scripts/menus.js"></script>
<script src="../scripts/budget.js"></script>
<script src="../scripts/jQnumberFormat.js"></script>
<script type="text/javascript">
    var existingAPs = <?=$jsAPAccts;?>;
<?php if ($nonmonthly) : ?>
    var nonm_apacct = <?=$js_nmapacct;?>;
    var nonm_aptype = <?=$js_nmaptype;?>;
    var nonm_apdays = <?=$js_nmapdays;?>;
    var nonm_apnext = <?=$js_nmapdues;?>;
<?php endif; ?>
</script>

</body>

</html>
