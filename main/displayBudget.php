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
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous" />
    <link href="../styles/charges.css" type="text/css" rel="stylesheet" />
    <link href="../styles/budget.css" type="text/css" rel="stylesheet" />
    <link href="../styles/modals.css" type="text/css" rel="stylesheet" />
    <script src="../scripts/jquery-1.12.1.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
</head>

<body>
    <nav class="navbar navbar-expand-md navbar-dark sticky-top"
        style="background-color:#004a00;">
    <div class="container-fluid">
        <a class="navbar-brand" style="color:#e5c063" href="#">Budgetizer</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarNav" aria-controls="navbarNav"
            aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle active" href="#"
                    id="expenses" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Expenses
                </a>
                <ul class="dropdown-menu" aria-labelledby="expenses">
                    <li><a id="chgexp" class="dropdown-item" href="#">
                        Pay/Charge An Expense</a></li>
                    <li><a class="dropdown-item"
                        href="../utilities/viewCharges.php" target="_self">
                        View/Edit Expenses</a></li>
                    <li><a class="dropdown-item"
                        href="../utilities/reverseCharge.php" target="_self">
                        Reverse Credit Charge</a></li>
                    <li><a class="dropdown-item"
                        href="../utilities/undoExpense.php" target="_self">
                        Undo Debit or Draft</a></li>
                    <li><a class="dropdown-item"
                        href="../edit/editCreditCharges.php" target="_self">
                        Edit Current Charges</a></li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle active" href="#"
                    id="allincome" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Income
                </a>
                <ul class="dropdown-menu" aria-labelledby="allincome">
                    <li><a id="reginc" class="dropdown-item"
                        href="#">Monthly</a></li>
                    <li><a id="onetimer" class="dropdown-item"
                        href="#">Other Deposits</a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a id="transfers" class="nav-link active" href="#">Transfers</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle active" href="#"
                    id="allcds" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Cards
                </a>
                <ul class="dropdown-menu" aria-labelledby="allcds">
                    <li><a id="cd2rec" class="dropdown-item"
                        href="#">Reconcile A Card</a></li>
                    <li><a id="addcrdr" class="dropdown-item" href="#">
                        Add a Card</a></li>
                    <li><a id="dac" class="dropdown-item" href="#">
                        Delete A Card</a></li>
                    <li><a id="addauto" class="dropdown-item" href="#">
                        Add Autopay</a></li>
                    <li><a id="rmap" class="dropdown-item" href="#">
                        Delete Autopay</a></li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle active" href="#"
                    id="budmgr" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Budget Mgr
                </a>
                <ul class="dropdown-menu" aria-labelledby="budmgr">
                    <li><a id="add1" class="dropdown-item"
                        href="#">Add Account</a></li>
                    <li><a id="del1" class="dropdown-item"
                        href="#">Delete Account</a></li>
                    <li><a id="moveit" class="dropdown-item"
                        href="#">Move Account</a></li>
                    <li><a id="ren1" class="dropdown-item"
                        href="#">Rename Account</a></li>
                    <li><a class="dropdown-item"
                        href="../edit/budgetEditor.php">Edit Budget Amounts</a>
                    </li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle active" href="#"
                    id="reports" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Reports
                </a>
                <ul class="dropdown-menu" aria-labelledby="reports">
                    <li><a id="mexpense" class="dropdown-item"
                        href="#">Monthly Expenses</a></li>
                    <li><a id="annual" class="dropdown-item"
                        href="#">Annual Expenses</a></li>
                    <li><a id="yrinc" class="dropdown-item"
                        href="#">Income Report</a>
                    </li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle active" href="#"
                    id="helper" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Help
                </a>
                <ul class="dropdown-menu" aria-labelledby="helper">
                    <li><a id="logout" class="dropdown-item" href="#">
                        Log out</a></li>
                    <li><a id="rpass" class="dropdown-item" href="#">
                        Change Password</a></li>
                    <li><a class="dropdown-item"
                        href="../help/help.php?doc=FAQ.pdf" target="_blank">
                        FAQ's</a></li>
                    <li><a class="dropdown-item disabled"
                        href="../help/help.php?doc=Tools.pdf" target="_blank">
                            Using Budgetizer</a></li>
                    <li><a class="dropdown-item"
                        href="../help/help.php?doc=HowToBudget.pdf" target="_blank">
                            Intro to Budgeting</a>
                    </li>
                    <li><a id="chgcookie" class="dropdown-item"
                        href="#"><span id="chglink">Reject Cookies</span></a></li>
                </ul>
            </li>
        </ul>
        </div>
    </div>
    </nav>
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

    <?php require "bootstrapModals.html"; ?>

    <div id="preloader">
        <img src="../images/preload.gif" alt="waiting..." />
    </div>
    <p style="clear:left;margin-left:16px;">
        <a href="http://validator.w3.org/check?uri=referer">
            <img src="http://www.w3.org/Icons/valid-xhtml10"
            alt="Valid XHTML 1.0 Strict" height="31" width="88" />
        </a>
    </p>
    <script src="../scripts/budget.js" type="text/javascript"></script>
    <script src="../scripts/jQnumberFormat.js" type="text/javascript"></script>
</body>
</html>