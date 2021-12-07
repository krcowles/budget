<?php
/**
 * This page is invoked when a new user successfully signs up, or, when a new
 * user has 'returned' after having partially entered data, saved and then exited.
 * The state of initial budget completion is tracked by the 'setup' field in the 
 * 'Users' table of the database. When registered, default accounts are established
 * and the setup = '100'. This page allows the user to establish preliminary data
 * for the new budget in three steps:
 * 1. The user can enter preliminary account data to display and manipulate.
 *    The budget is limited to basic data at this point. On first-time entry,
 *    the default budget items (Temporary Accounts and Undistributed Funds)
 *    are created automatically.
 *    >>> When working in this mode, setup = '100'
 * 2. The user can then enter preliminary card data (simple name & type)
 *    >>> When working in lv2, lv1 is finished and setup = '010'
 * 3. The user can then enter current outstanding/unpaid credit card charges
 *    >>> When working in lv3, lv1 & lv2 are finished and setup = '001'
 * PHP Version 7.8
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require_once "../database/global_boot.php";

// If the user just registered, the query string pnl=000
$pnl    = isset($_GET['pnl'])    ? filter_input(INPUT_GET, 'pnl') : "000";

// $lv_ values are used by newBudget.js to determine which panel to open
$lv1 = $pnl[0] === '1' ? 'yes' : 'no';
$lv2 = $pnl[1] === '1' ? 'yes' : 'no';
$lv3 = $pnl[2] === '1' ? 'yes' : 'no';
// first time entry only (user just registered), $pnl === '000'
$new = $pnl === '000' ? true : false; 

if ($new) {
    // Create Undistributed Funds account and Temp Accounts
    $lastpos = 0;
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
    // 'Tmp_' funds accounts
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
    // prevent re-creating these preliminary accounts by ensuring that 'setup' 
    // is no longer '000'; Start with '100': may change in subsequent sections
    $setup = '100';
    $no_bud_dat = "nodat";
    $no_crd_dat = "nocrds";
    $_SESSION['start'] = $setup;
    $initReq = "UPDATE `Users` SET `setup` = :setup WHERE `uid` = :uid;";
    $init = $pdo->prepare($initReq);
    $init->execute(["setup" => $setup, "uid" => $_SESSION['userid']]);
    // For new accounts, there are no budget options yet...
    $budsel = '<select class="budsel" name="chgto[]">' . PHP_EOL;
    $budsel .= '<option value="NOBUD">No Budgets Entered</option>' . PHP_EOL;
    $budsel .= '</select>' . PHP_EOL;
} else {
    // get any budget data already entered (if any) - excludes default accounts
    $setup = $pnl; // $_SESSION['start'] will already be correctly assigned
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
    // unique name attr's required: one for new expenses, one for old
    $budsel = '<select class="budsel" name="chgto[]">' . PHP_EOL;
    $budsel .= '<option value="NOBUD">Select Account</option>' . PHP_EOL;
    $budchg = '<select class="oldbud" name="oldchg[]">' . PHP_EOL;
    foreach ($old_dat as $old) {
        array_push($aeIds, $old['id']);
        array_push($aePos, intval($old['budpos']));
        array_push($aeNames, $old['budname']);
        array_push($aeBudamt, $old['budamt']);
        array_push($aeCurr, $old['current']);
        $budsel .= '<option value="' . $old['budname'] . '">' .
            $old['budname'] . '</option>' . PHP_EOL;
        $budchg .= '<option value="' . $old['budname'] . '">' .
            $old['budname'] . '</option>' . PHP_EOL;
    }
    if ($aedata) {
        $lastpos = max($aePos);
        $no_bud_dat = "dat";
    } else {
        $lastpos = 0;
        $no_bud_dat = "nodat";
        $budsel .= '<option value="NOBUD">No Budgets Entered</option>' . PHP_EOL;
        $budchg .= '<option value="NOBUD">No Budgets Entered</option>' . PHP_EOL;
    }
    $budsel .= '</select>' . PHP_EOL;
    $budchg .= '</select>' . PHP_EOL;

    // get any card data already entered (if any)
    include "../utilities/getCards.php"; // sets up select boxes
    $cdIds = [];
    $cdNames = [];
    $cdTypes = [];
    // $cards contains all data, if any, obtained via getCards.php
    $aecards = count($cards) > 0 ? true : false;
    $no_crd_dat = $aecards ? 'crds' : 'nocrds';
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
    $aeAcct = [];
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
        array_push($aeAcct, $expense['acctchgd']);
    }
}
?>
<!DOCTYPE html>
<html lang="en-us">

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
<div id="content">
    <p id="pnlin"><?=$pnl;?></p>
    <p id="curr_setup"><?=$setup;?></p>
    <p id="no_bud_dat"><?=$no_bud_dat;?></p>
    <p id="no_crd_dat"><?=$no_crd_dat;?></p>
    <p id="ready" class="LargeHeading">You're Ready To Start!&nbsp;&nbsp;
        <span id="help">[New to Budgeting? See
        <a href="../help/help.php?doc=HowToBudget.pdf" target="_blank">How
        to Budget</a>]</span>
    </p>
    <h2>Click on the three simple steps below to get started</h2>
    <h3><button id="done">Data Entry Finished</button>
        &nbsp;&nbsp;Go to budget page</h3>
   
    <?php if ($new || !$aedata) : ?>
        <div id="one" class="steps">1. Create the basic budget
            <span class="hilite">&nbsp;&nbsp;[No User Budget Data Entered]</span>
        </div>
    <?php else : ?>
        <div id="one" class="steps">Add Budget Data</div>
    <?php endif; ?>
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
                you  may continue to add/edit data.
            </span><br /><br />
            <div id="old">
                <?php if (!$new) {
                    include "enteredBudget.php";
                } ?>
            </div>
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
        </form>
    </div>

    <?php if ($new || !$aecards) : ?>
        <div id="two" class="steps">2. Add Credit/Debit Cards
            <span class="hilite">&nbsp;&nbsp;[No Credit/Debit Cards Entered]</span>
        </div>
    <?php else : ?>
        <div id="two" class="steps">Add/Edit Credit/Debit Cards</div>
    <?php endif; ?>
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
                you  may continue to add/edit data.
            </span><br /><br />
            <div id="cold">
                <?php if (!$new) {
                    include "enteredCards.php";
                } ?>
            </div>
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
        </form>
    </div>

    <?php if ($new || !$aeexp) : ?>
        <div id="three" class="steps">3. Enter Outstanding/Unpaid Charges
            <span class="hilite">&nbsp;&nbsp;[No Charges Entered]</span></div>
    <?php else : ?>
        <div id="three" class="steps">Add/Edit Outstanding/Unpaid Charges</div> 
    <?php endif; ?>
    <div id="expenses">
        <?php require "../utilities/getCards.php"; ?>
        <span class="note NormalHeading">Note: If you make changes,
            be sure to 'Save'!
        </span><br />
        <form id="edform" method="post" action="saveNewCharges.php">
            <input type="hidden" name="lv3" value="<?=$lv3;?>" />
            <input type="hidden" name="exit3" value="no" />
            <button id="save">Save</button>
            <span><button id="lv3">Save and Return Later</button>
            </span><br />
            <span class="selnote">Note: When you click on 'Save and Continue',
                the data you entered, and any edits, will be 
                saved to your budget, and new entries will become available.
                If you 'Save and Return Later', all data will be saved and
                you will leave the site. When you later return to the Budgetizer,
                you  may continue to add/edit data.
            </span><br /><br />
            <div id="eold">
                <?php if (!$new) {
                    include "enteredCharges.php";
                } ?>
            </div>
            <div id="enew">
                <span class="NormalHeading">Enter your new expense information
                    below. (Outstanding/unpaid Credit charges only)</span>
                <br /><br />
                <?php for ($z=0; $z<4; $z++) : ?>
                    Credit Card Used:
                    <span id="ncd<?=$z;?>"><?=$ccHtml;?></span>&nbsp;&nbsp;
                    Date of Expense: <input type="text" name="edate[]"
                        class="datepicker dates" />&nbsp;&nbsp;
                    Amount Paid: <input type="text" name="eamt[]" 
                        class="inp amts" />&nbsp;&nbsp;
                    Payee: <input type="text" name="epay[]" 
                        class="inp" /><br />
                    Charge to: <?=$budsel;?><br />
                <?php endfor; ?>
            </div>
        </form>
    </div>
</div>

<script src="../scripts/jquery.min.js"></script>
<script src="../scripts/jquery-ui.js"></script>
<script src="../scripts/dbValidation.js"></script>
<script src="../scripts/newBudget.js"></script>

</body>

</html>
