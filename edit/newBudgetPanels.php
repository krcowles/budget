<?php
/**
 * This page is invoked when a new user successfully signs up, or, when a new
 * user has 'returned' after having partially entered data, saved and then exited.
 * The state of initial budget completion is tracked by the 'setup' field in the 
 * 'Users' table of the database. The page allows the user to establish
 * preliminary data for the new budget in three steps:
 * 1. The user can enter preliminary account data to display and manipulate.
 *    The budget is limited to basic data at this point. On first-time entry,
 *    the default budget items (Temporary Accounts and Undistributed Funds)
 *    are created automatically.
 *    >>> When working in this mode, setup = '100'
 * 2. The user can enter preliminary card data (simple name & type)
 *    >>> When working in lv2, lv1 is finished and setup = '110'
 * 3. The user can enter current outstanding/unpaid credit card charges
 *    >>> When working in lv3, lv1 & lv2 are finished and setup = '001'
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require_once "../database/global_boot.php";

$pnl    = isset($_GET['pnl'])    ? filter_input(INPUT_GET, 'pnl') : "000";

// $lvx values are used by newBudget.js to determine which panel to open
$lv1 = $pnl === '100' ? 'yes' : 'no';
$lv2 = $pnl === '110' ? 'yes' : 'no';
$lv3 = $pnl === '001' ? 'yes' : 'no';
// first time entry only, when $pnl === '000'
$new = $pnl === '000' ? true : false; 
if ($new) {
    $lastpos = 0;
}

if ($new) {
    // 'Undistributed funds' account initial settings
    $undis = array(  
        '`userid`'  =>  $_SESSION['userid'],
        '`budname`' => "'Undistributed Funds'",
        '`budpos`'  => "'30000'",
        '`status`'  => "'T'",
        '`budamt`'  => "'0'",
        '`prev0`'   => "'0'",
        '`prev1`'   => "'0'",
        '`current`' => "'0'",
        '`autopay`' => "''",
        '`moday`'   => "'0'",
        '`autopd`'  => "''",
        '`funded`'  => "'0'"
    );
    $columns = implode(",", array_keys($undis));
    $values = implode(",", array_values($undis));
    $sql = "INSERT INTO `Budgets` (" . $columns .  ") VALUES (" . $values . ");";
    $addUndis = $pdo->query($sql);
    // temporary funds accounts
    for ($i=1; $i<6; $i++) {
        $tname = 'Tmp' . $i;
        $tname = "'" . $tname . "'";
        $tno = 30000 + $i;
        $tno = "'" . $tno . "'";
        $tacct = array(
            $_SESSION['userid'], $tname, $tno, 
            "'T'", "'0'", "'0'", "'0'", "'0'", "''", "'0'", 
            "''", "'0'"
        );
        $values = implode(",", $tacct);
        $sql = "INSERT INTO `Budgets` (" . $columns . ") VALUES (" . $values . ");";
        $addTemp = $pdo->query($sql);
    }
} else {
    // get any budget data already entered (if any)
    $aeIds = [];
    $aeNames = [];
    $aeBudamt = [];
    $aeCurr = [];
    $aePos = [];
    $sql = "SELECT * FROM `Budgets` WHERE `userid` = :uid AND `status` = 'A';";
    $stmnt = $pdo->prepare($sql);
    $stmnt->execute(["uid" => $_SESSION['userid']]);
    $old_dat = $stmnt->fetchALL(PDO::FETCH_ASSOC);
    $aedata = count($old_dat) > 0 ? true : false;
    foreach ($old_dat as $old) {
        array_push($aeIds, $old['id']);
        array_push($aePos, intval($old['budpos']));
        array_push($aeNames, $old['budname']);
        array_push($aeBudamt, $old['budamt']);
        array_push($aeCurr, $old['current']);
    }
    if ($aedata) {
        $lastpos = max($aePos);
    } 
    // get any card data already entered (if any)
    include "../utilities/getCards.php";
    $cdIds = [];
    $cdNames = [];
    $cdTypes = [];
    $cds = "SELECT * FROM `Cards` WHERE `userid` = :uid;";
    $cddat = $pdo->prepare($cds);
    $cddat->execute(["uid" => $_SESSION['userid']]);
    $cards = $cddat->fetchALL(PDO::FETCH_ASSOC);
    $aecards = count($cards) > 0 ? true : false;
    foreach ($cards as $card) {
        array_push($cdIds, $card['cdindx']);
        array_push($cdNames, $card['cdname']);
        array_push($cdTypes, $card['type']);
    }

    // get any expenses data already entered (if any)
    $exIds = [];
    $aeCard = [];
    $aeDate = [];
    $aeAmt = [];
    $aePayee = [];
    $exp = "SELECT * FROM `Charges` WHERE `userid` = :uid;";
    $expdat = $pdo->prepare($exp);
    $expdat->execute(["uid" => $_SESSION['userid']]);
    $expenses = $expdat->fetchALL(PDO::FETCH_ASSOC);
    $aeexp = count($expenses) > 0 ? true : false;
    foreach ($expenses as $expense) {
        array_push($exIds, $expense['expid']);
        array_push($aeCard, $expense['cdname']);
        array_push($aeDate, $expense['expdate']);
        array_push($aeAmt, $expense['expamt']);
        array_push($aePayee, $expense['payee']);
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>New Account Data</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Rolling 3-month budget tracker" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <link href="../styles/standards.css" type="text/css" rel="stylesheet" />
    <link href="../styles/jquery-ui.css" type="text/css" rel="stylesheet" />
    <link href="../styles/newBudget.css" type="text/css" rel="stylesheet" />
</head>

<body>
<div id="intro">
    <p id="ready" class="LargeHeading">You're Ready To Start!&nbsp;&nbsp;
        <span id="help">[New to Budgeting? See
        <a href="../help/help.php?doc=HowToBudget.pdf" target="_blank">How
        to Budget</a>]</span>
    </p>
    <h2>Click on these three simple steps (in order) to get started:
        <span>
            <button id="done">Completed: Go To Budget</button>
        </span>
    </h2>
    
    <div id="one" class="steps">Create the basic budget</div>
    <div id="budget">
        <span class="note NormalHeading">Note: If you make changes,
            be sure to 'Save'!
        </span><br />
        <form id="form" action="saveNewBudget.php" method="post">
            <input type="hidden" name="lastpos" value="<?=$lastpos;?>" />
            <input type="hidden" name="lv1" value="<?=$lv1;?>" />
            <input type="hidden" name="exit1" value="no" />
            <button id="save1">Save and Continue</button>
            <span><button id="lv1">Save and Return Later</button>
            </span><br />
            <span class="selnote">Note: When you click on 'Save and Continue',
                the data you entered, and any edits, will be 
                saved to your budget, and new entries will become available.
                If you 'Save and Return Later', all data will be saved and
                you will leave the site. When you later return to the Budgetizer,
                you will be brought to this page to complete your data entry.
            </span><br /><br />
            <div id="new">
                <span class="NormalHeading">Enter your new budget information
                    below. Please use whole numbers only (integers) for "Monthly
                    Budget" amounts. "Current Value" entries can be 
                    'dollars and cents'.
                </span><br />
                <div id="buditems">
                    <?php for ($q=0; $q<5; $q++) : ?>
                        Budget Item: <input class="acctname" type="text" 
                        name="acctname[]" />
                        Monthly Budget: $ <input class="bud" type="text"
                            name="bud[]"/>
                        Current value: $ <input class="bal" type="text"
                            name="bal[]" />
                        <br /><br />
                    <?php endfor; ?>
                </div><br />
            </div>
            <div id="old">
                <?php if (!$new) {
                    include "enteredBudget.php";
                } ?>
            </div>
        </form>
    </div>

    <div id="two" class="steps">Add Credit/Debit Cards</div>
    <div id="cards">
        <span class="note NormalHeading">Note: If you make changes,
            be sure to 'Save'!
        </span><br />
        <form id="cdform" method="post" action="saveNewCards.php">
            <input type="hidden" name="lv2" value="<?=$lv2;?>" />
            <input type="hidden" name="exit2" value="no" />
            <button id="nocds">No Cards to Enter</button>
            <button id="save2">Save and Continue</button>
            <span><button id="lv2">Save and Return Later</button>
            </span><br />
            <span class="selnote">Note: When you click on 'Save and Continue',
                the data you entered, and any edits, will be 
                saved to your budget, and new entries will become available.
                If you 'Save and Return Later', all data will be saved and
                you will leave the site. When you later return to the Budgetizer,
                you will be brought to this page to complete your data entry.
            </span><br /><br />
            <div id="cnew">
                <span class="NormalHeading">Enter your new card information
                    below.</span><br />
                <?php for ($y=0; $y<4; $y++) : ?>
                    New Card Name: <input type="text" name="cname[]" />
                    Card Type: <select name="ctype[]">
                        <option value="Credit">Credit</option>
                        <option value="Debit">Debit</option>
                    </select><br /><br />
                <?php endfor; ?>
            </div>
            <div id="cold">
                <?php if (!$new) {
                    include "enteredCards.php";
                } ?>
            </div>
        </form>
    </div>

    <div id="three" class="steps">Enter/Edit Outstanding/Unpaid Charges</div>
    <div id="expenses">
        <?php require "../utilities/getCards.php"; ?>
        <span class="note NormalHeading">Note: If you make changes,
            be sure to 'Save'!
        </span><br />
        <form id="edform" method="post" action="saveNewCharges.php">
            <input type="hidden" name="lv3" value="<?=$lv3;?>" />
            <input type="hidden" name="exit3" value="no" />
            <button id="save">Save and Continue</button>
            <span><button id="lv3">Save and Return Later</button>
            </span><br />
            <span class="selnote">Note: When you click on 'Save and Continue',
                the data you entered, and any edits, will be 
                saved to your budget, and new entries will become available.
                If you 'Save and Return Later', all data will be saved and
                you will leave the site. When you later return to the Budgetizer,
                you will be brought to this page to complete your data entry.
            </span><br /><br />
            <div id="enew">
                <span class="NormalHeading">Enter your new expense information
                    below. (Outstanding/unpaid Credit charges only)</span><br />
                <?php for ($z=0; $z<4; $z++) : ?>
                    Credit Card Used:
                    <span id="ncd<?= $z;?>"><?= $ccHtml;?></span>&nbsp;&nbsp;
                    Date of Expense: <input type="text" name="edate[]"
                        class="datepicker dates" />&nbsp;&nbsp;
                    Amount Paid: <input type="text" name="eamt[]" 
                        class="inp amts" />&nbsp;&nbsp;
                    Payee: <input type="text" name="epay[]" 
                        class="inp" /><br /><br />
                <?php endfor; ?>
            </div>
            <div id="eold">
                <?php if (!$new) {
                    include "enteredCharges.php";
                } ?>
            </div>
        </form>
    </div>
</div>

 <p style="clear:left;margin-left:16px;">
    <a href="http://validator.w3.org/check?uri=referer">
        <img src="http://www.w3.org/Icons/valid-xhtml10"
        alt="Valid XHTML 1.0 Strict" height="31" width="88" />
    </a>
</p>

<script src="../scripts/jquery-1.12.1.js" type="text/javascript"></script>
<script src="../scripts/jquery-ui.js" type="text/javascript"></script>
<script src="../scripts/dbValidation.js" type="text/javascript"></script>
<script src="../scripts/newBudget.js" type="text/javascript"></script>

</body>

</html>
