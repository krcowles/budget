<?php
/**
 * These modals are packaged for use by bootstrap
 * PHP Version 8.3.9
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
?>
<!-- Autopays Pending modal -->
<div id="presentap" class="modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Items Due for Autopay</h5>
                <button type="button" style="visibility:hidden;" 
                    class="btn">INVISIBLE BTN SPACE UTILIZATION</button>
                <button id="iteration" type="button" class="btn 
                    btn-secondary">Don't show again this session
                </button>
                
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="apitems" class="modal-body">
                <table id="aptbl">
                    <tbody id="apbody">
                    </tbody>
                </table>                
            </div>
            <div class="modal-footer">
                <button id="appaybtn" type="button" class="btn btn-success">
                    Pay Checked Items</button>
                <button id="payap" type="button" class="btn btn-danger"
                    data-bs-dismiss="modal">No Payments</button>
            </div>
        </div>
    </div>
</div>
<!-- Charge Expense Modal -->
<div id="expmodal" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Charge or Pay An Expense</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table id="modexptbl" class="modals exptbl">
                    <tr>
                        <td style="text-align:left;">Deduct From:</td>
                        <td style="text-align:left;"><div id="fsel0">
                            <?= $fullsel;?></div></td>
                    </tr>
                    <tr>
                        <td style="text-align:left;">Pay With</td>
                        <td style="text-align:left;"><div id="csel0">
                            <?= $allCardsHtml;?></div></td>
                    </tr>
                    <tr>
                        <td style="text-align:left;">Amount to pay:</td>
                        <td style="text-align:left;"><input type="text"
                            id="expamt" /></td>
                    </tr>
                    <tr>
                        <td style="text-align:left;">Paid to:</td>
                        <td style="text-align:left;"><input type="text"
                            id="exppayto" /></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Close</button>
                <button id="pebtn" type="button" class="btn btn-success">Pay</button>
            </div>
        </div>
    </div>
</div>
<!-- Non-Monthlies Expense Modal -->
<div id="nmexp" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pay Non-Monthly Expense</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="modals exptbl">
                    <tr>
                        <td style="text-align:left;">Deduct From:</td>
                        <td style="text-align:left;"><div id="dedsel0">
                            <?=$nmsel;?></div></td>
                    </tr>
                    <tr>
                        <td style="text-align:left;">Pay With</td>
                        <td style="text-align:left;"><div id="nmsel0">
                            <?= $allCardsHtml;?></div></td>
                    </tr>
                    <tr>
                        <td style="text-align:left;">Amount to pay:</td>
                        <td style="text-align:left;"><input type="text"
                            id="nmexpamt" /></td>
                    </tr>
                    <tr>
                        <td style="text-align:left;">Paid to:</td>
                        <td style="text-align:left;"><input type="text"
                            id="nmexppayto" /></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Close</button>
                <button id="nmpebtn" type="button"
                    class="btn btn-success">Pay</button>
            </div>
        </div>
    </div>
</div>
<!-- Monthly Income Modal -->
<div id="incmodal" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Deposit Regular Monthly Income</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div>Enter Income Amount:&nbsp;&nbsp;<input id="incdep"
                    type="text" /></div>
                <div>Defer automatic distribution until 
                    <span id="defermo"></span>&nbsp;&nbsp;
                    <input id="defer" type="checkbox" /><br />
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Close</button>
                <button id="incbtn" type="button" class="btn btn-success">
                    Deposit</button>
            </div>
        </div>
    </div>
</div>
<!-- Irregular Deposits Modal -->
<div id="othrdeps" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">One-Time Deposits</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div>Enter amount deposited:&nbsp;&nbsp;<input type="text"
                    id="onedep" /></div><br />
                <div>Memo:&nbsp;&nbsp;<input type="text"
                    id="otmemo" /></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Close</button>
                <button id="otbtn" type="button" class="btn btn-success">
                    Deposit</button>
            </div>
        </div>
    </div>
</div>
<!-- Undo an Irregular Deposit -->
<div id="removeinc" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Undo a Deposit</h5>
                <button type="button" class="btn-close"
                        data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <span style="font-style:italic">NOTE: This applies only to
                    non-monthly deposits. Funds will be removed from the
                    "Undistributed Funds" account.</span><br />
                <hr class="dropdown-divider">
                <div id="inclist">Recent Non-monthly deposits:
                    <table id="irdeps"></table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Close</button>
                <button id="selinc" type="button" class="btn btn-success">
                    Undo</button>
            </div>
        </div>
    </div>
</div>
<!-- Transfer Funds Modal -->
<div id="xfrfunds" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Move Funds</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Transfer the following amount:&nbsp;&nbsp;$ <input type="text" 
                    id="xframt" /><br />
                <span id="xfrfrom">Take from:&nbsp;&nbsp;
                    <?=$fullsel;?></span><br />
                <span id="xfrto">Place in:&nbsp;&nbsp;<?=$fullsel;?></span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Close</button>
                <button id="xfrbtn" type="button" class="btn btn-success">
                    Transfer</button>
            </div>
        </div>
    </div>
</div> 
<!-- Credit Card Reconciliation Modal -->
<div id="reconcile" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reconcile Credit Card</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Please select the card to reconcile:&nbsp;&nbsp;
                <span id="ccsel0"><?=$ccHtml;?></span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Close</button>
                <button id="recbtn" type="button" class="btn btn-success">
                    Reconcile</button>
            </div>
        </div>
    </div>
</div>
<!-- Add a Card Modal -->
<div id="addacard" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add A Card</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Enter a unique name:&nbsp;&nbsp;<input id="cda"
                    type="text" /><br />
                <span id="adder">Select the type of card:&nbsp;&nbsp;
                <select id="cdprops">
                    <option value="Credit">Credit</option>
                    <option value="Debit">Debit</option>
                </select></span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Close</button>
                <button id="addcdbtn" type="button" class="btn btn-success">
                    Add Card</button>
            </div>
        </div>
    </div>
</div> 
<!-- Delete A Card Modal -->
<div id="deletecd" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete A Card</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Select the card you wish to delete:&nbsp;&nbsp;
                <span id="deletecard"><?= $allCardsHtml;?></span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Close</button>
                <button id="dcbtn" type="button" class="btn btn-success">
                    Delete Card</button>
            </div>
        </div>
    </div>
</div> 
<!-- Modify an Autopay Modal -->  
<div id="modap" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modify Existing Autopay</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="mapitem">Select Autopay Account
                    <?=$apacctsel;?></div><br />
                <div id="maselap">Method to be applied:
                <?= $allCardsHtml;?></div>
                <div>on day
                <input id="newapday" type="text" />
                    of each month.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Close</button>
                <button id="modapbtn" type="button" class="btn btn-success">
                    Modify Autopay</button>
            </div>
        </div>
    </div>
</div> 
<!-- Add an Autopay Modal -->
<div id="auto" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Autopay</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="apsel">Prompt for automatic payment of:&nbsp;&nbsp;
                    <?= $fullsel;?></div>
                <div id="ccselap">Method to be applied:
                <?= $allCardsHtml;?></div>
                <div>on day
                <input id="useday" type="text" />
                    of each month.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Close</button>
                <button id="addapbtn" type="button" class="btn btn-success">
                    Start Autopay</button>
            </div>
        </div>
    </div>
</div> 
<!-- Delete an Autopay Modal -->
<div id="deleteauto" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Autopay</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Select the account for which you wish to delete
                autopay:
                <div id="delapacct"><?=$apacctsel;?></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Close</button>
                <button id="delapbtn" type="button" class="btn btn-success">
                    Delete Autopay</button>
            </div>
        </div>
    </div>
</div> 
<!-- Add an Account Modal -->
<div id="addacct" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Account</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div>Enter the account information for the new addition to the
                budget:</div><br />
                <div id="ana">New Account Name:&nbsp;&nbsp;
                <input id="newacct" type="text" /></div>
                Enter the monthly amount (whole dollars only)<br />
                <span>to be budgeted to this account:&nbsp;&nbsp;
                $ <input id="mo" type="text" /></span><br /><br />
                <div id="aanote"> Use 'Transfer Funds' to establish
                this month's balance, or other tools to modify
                other features.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Close</button>
                <button id="addactbtn" type="button" class="btn btn-success">
                    Add Account</button>
            </div>
        </div>
    </div>
</div>
<!-- Delete an Account Modal -->
<div id="deleteacct" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Account</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div>Note: you can only delete accounts you create (i.e. not
                'Undistributed Funds' or 'Temporary Accounts')</div><br />
                <span style="color:brown;">NOTE: Please Ensure the
                budget-to-delete has a balance of $0.</span><br /><br />
                <div id="remacct">Delete:&nbsp;&nbsp;<?=$partsel;?></div><br />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Close</button>
                <button id="daccbtn" type="button" class="btn btn-success">
                    Delete Account</button>
            </div>
        </div>
    </div>
</div>
<!-- Move an Account Modal -->
<div id="moveacct" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Move Account</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div>Note: you cannot move the 'Undistributed Funds' nor any
                'Temporary Account'</div>
                <div id="mvfrom">Place the following account:&nbsp;&nbsp;
                    <?= $partsel;?></div><br />
                <div id="mvto">Directly above:&nbsp;&nbsp;
                    <?= $partsel;?></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Close</button>
                <button id="mvbtn" type="button" class="btn btn-success">
                    Move Account</button>
            </div>
        </div>
    </div>
</div>
<!-- Rename an Account Modal -->
<div id="renameacct" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rename Account</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <span id="asel">Select the account you wish to rename: 
                <?= $fullsel;?></span><br /><br />
                Supply the new name: <input id="newname" type="text" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Close</button>
                <button id="renbtn" type="button" class="btn btn-success">
                    Rename Account</button>
            </div>
        </div>
    </div>
</div>
<!-- Move an Account Modal -->
<div id="moexp" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Monthly Expenses</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
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
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Close</button>
                <button id="mexpbtn" type="button" class="btn btn-success">
                    Generate Report</button>
            </div>
        </div>
    </div>
</div>
<!-- Annual Expenses Modal -->
<div id="annexp" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Annual Expenses</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Please select the report year<br />
                <select id="rptyr" name="year">
                    <option value="<?=$thisyear;?>"><?=$thisyear;?></option>
                    <option value="<?=$prioryr1;?>"><?=$prioryr1;?></option>
                    <option value="<?=$prioryr2;?>"><?=$prioryr2;?></option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Close</button>
                <button id="anexpbtn" type="button" class="btn btn-success">
                    Generate Report</button>
            </div>
        </div>
    </div>
</div>
<!-- Annual Income Modal -->
<div id="anninc" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Annual Income</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Select a year to view:<br />
                <select id="incyear" name="incyear">
                    <option value="<?=$thisyear;?>"><?=$thisyear;?></option>
                    <option value="<?=$prioryr1;?>"><?=$prioryr1;?></option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Close</button>
                <button id="aincbtn" type="button" class="btn btn-success">
                    Generate Report</button>
            </div>
        </div>
    </div>
</div>
<!-- Transfer History Modal -->
<div id="annxfrs" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yearly Transfers</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Select a year to view:<br />
                <select id="xfryr" name="xfryr">
                    <option value="<?=$thisyear;?>"><?=$thisyear;?></option>
                    <option value="<?=$prioryr1;?>"><?=$prioryr1;?></option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Close</button>
                <button id="yrlyXfrbtn" type="button" class="btn btn-success">
                    Generate Report</button>
            </div>
        </div>
    </div>
</div>
<!-- Change Password Modal -->
<div id="resetemail" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                You will be sent an email with your account name and a link
                to reset your password.<br />
                Your email: <input id="remail" type="email" />
            </div>
            <div class="modal-footer">
                <button id="chgclose" type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Close</button>
                <button id="cpass" type="button"
                    class="btn btn-success">Send Email</button>
            </div>
        </div>
    </div>
</div>
<!-- Security Questions Modal -->
<div id="security" class="modal" tabindex="-1">
    <div class="modal-dialog" style="max-width:60%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Answer 3 Security Questions</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="uques"></div>
            </div>
            <div class="modal-footer">
                <button id="resetans" type="button" class="btn btn-secondary">
                    Reset Answers</button>
                <button id="closesec" type="button" class="btn btn-secondary">
                    Apply</button>
            </div>
        </div>
    </div>
</div>
