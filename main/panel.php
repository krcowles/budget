<?php
/**
 * This script presents the html that comprises the top-of-the-page navigation.
 * The menus have some variable content controlled by php and javascript:
 * e.g. icon showing which page is currently active; etc.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
?>
<div id="panel">
    <div id="navbar">
        <ul id="allMenus">
            <li id="expense" class="menu-main">
                <div class="menu-item">
                    <span class="menu-text">Pay<br />Expense</span>
                </div>
            </li>
            <li id="moinc" class="menu-main">
                <div class="menu-item">
                    <span class="menu-text">Deposit<br />Monthly Income</span>
                </div>
            </li>
            <li id="otd" class="menu-main">
                <div class="menu-item">
                    <span class="menu-text">One-time<br />Deposit</span>
                </div>
            </li>
            <li id="movefnds" class="menu-main">
                <div class="menu-item">
                    <span class="menu-text">Tranfer<br />Funds</span>
                </div>
            </li>
            <li id="recon" class="menu-main">
                <div class="menu-item">
                    <span class="menu-text">Reconcile<br />Credit Card</span>
                </div>
            </li>
            <li id="schedap" class="menu-main">
                <div class="menu-item">
                    <span class="menu-text">Autopay</span>
                    <div class="menuIcons menu-open"></div>
                </div>
                <div id="menu-schedap" class="menu-default">
                    <ul class="menus">
                        <li><div id="sched">Schedule Autopay</div></li>
                        <li><div id="delap">Delete Autopay</div></li>
                    </ul>
                </div>
            </li>
            <li id="edcds" class="menu-main">
                <div class="menu-item">
                    <span class="menu-text">Edit Cards&nbsp;</span>
                    <div class="menuIcons menu-open"></div>
                </div>
                <div id="menu-edcds" class="menu-default">
                    <ul class="menus">
                        <li><div id="adcd">Add Card</div></li>
                        <li><div id="decd">Delete Card</div></li>
                    </ul>
                </div>
            </li>
            <li id="edbud" class="menu-main">
                <div class="menu-item">
                    <span class="menu-text">Edit Budget&nbsp;</span>
                    <div class="menuIcons menu-open"></div>
                </div>
                <div id="menu-edbud" class="menu-default">
                    <ul class="menus">
                        <li><div id="edbud">Edit Budget Entries</div></li>
                        <li><div id="edadd">Add Account</div></li>
                        <li><div id="eddel">Delete Account</div></li>
                        <li><div id="edmov">Move Account</div></li>
                        <li><div id="edrnm">Rename Account</div></li>
                    </ul>
                </div>
            </li>
            <li id="rpts" class="menu-main">
                <div class="menu-item">
                    <span class="menu-text">Expenses&nbsp;</span>
                    <div class="menuIcons menu-open"></div>
                </div>
                <div id="menu-rpts" class="menu-default">
                    <ul class="menus">
                        <li><div id="vexp">View Expenses</div></li>
                        <li><div id="lac">Add New Charges</div></li>
                        <li><div id="lin">Edit Charges</div></li>
                        <li><div id="lout">Monthly Report</div></li>
                        <li><div id="join">Annual Report</div></li>
                    </ul>
                </div>
            </li>
            <li id="help" class="menu-main">
                <div class="menu-item">
                    <span class="menu-text">Help&nbsp;</span>
                    <div class="menuIcons menu-open"></div>
                </div>
                <div id="menu-help" class="menu-default">
                <ul class="menus">
                    <li><div id="doc">Budgetizer Basics</div></li>
                    <li><div id="conv">Making a Budget</div></li>
                </ul>
                </div>
            </li>
        </ul>
    </div>
</div>
<br />
<br />
