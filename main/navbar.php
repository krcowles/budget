<?php
/**
 * The variables required by the navbar are created in two scripts:
 * getAccountData.php and getCards.php; these are required here.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require_once "../utilities/getAccountData.php";
require_once "../utilities/getCards.php";
?>
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
            <li id="bp" class="nav-item">
                <a href="../main/displayBudget.php" id="budpg"
                    class="nav-link active">Budget Page</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle active" href="#"
                    id="expenses" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Expenses
                </a>
                <ul class="dropdown-menu" aria-labelledby="expenses">
                    <li><a id="chgexp" class="dropdown-item" href="#">
                        Pay/Charge An Expense</a></li>
                    <li><a id="exp1" class="dropdown-item"
                        href="../utilities/viewCharges.php" target="_self">
                        View/Edit Expenses</a></li>
                    <li><a id="exp2" class="dropdown-item"
                        href="../utilities/reverseCharge.php" target="_self">
                        Reverse Credit Charge</a></li>
                    <li><a id="exp3" class="dropdown-item"
                        href="../utilities/undoExpense.php" target="_self">
                        Undo Debit or Draft</a></li>
                    <li><a id="exp4" class="dropdown-item"
                        href="../edit/editCreditCharges.php" target="_self">
                        Edit Current Charges</a></li>
                    <li><a id="upexp" class="dropdown-item"
                        href="../edit/editExpenses.php" target="_self">
                        Update 30-day Expenses</a></li>
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
                    <li><a id="undoinc" class="dropdown-item"
                        href="#">Undo a Deposit</a></li>
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
                    <li><a class="dropdown-item" id="edbuds"
                        href="../edit/budgetEditor.php">
                        Edit Budget Amts &amp; Balances</a>
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
