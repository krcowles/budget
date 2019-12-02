<?php
/**
 * This page is invoked when a new user successfully signs up. It allows
 * the user to establish preliminary data for the new budget:
 * 1. The user can enter preliminary account data to display and manipulate.
 *    The budget is limited to basic data at this point. On first-time entry,
 *    the default budget items are create automatically. 
 * 2. The user can enter preliminary card data (simple name & type)
 * 3. The user can enter current unpaid expenses
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require_once "../database/global_boot.php";

$user = filter_input(INPUT_GET, 'user');
$new  = isset($_GET['new']) ? true : false;
$pnl  = isset($_GET['pnl']) ? filter_input(INPUT_GET, 'pnl') : "none";
// On first invocation, create the default accounts for 'user'
if ($new) {
    $undis = array(
        '`user`' => "'" . $user . "'",
        '`budname`' => "'Undistributed Funds'",
        '`budpos`' => "'30000'",
        '`status`' => "'T'",
        '`budamt`' => "'0'",
        '`prev0`' => "'0'",
        '`prev1`' => "'0'",
        '`current`' => "'0'",
        '`autopay`' => "''",
        '`moday`' => "'0'",
        '`autopd`' => "''",
        '`funded`' => "'0'"
    );
    $columns = implode(",", array_keys($undis));
    $values = implode(",", array_values($undis));
    $sql = "INSERT INTO `Budgets` (" . $columns .  ") VALUES (" . $values . ");";
    $addUndis = $pdo->query($sql);
    for ($i=1; $i<6; $i++) {
        $usr = "'" . $user . "'";
        $tname = 'Tmp' . $i;
        $tname = "'" . $tname . "'";
        $tno = 30000 + $i;
        $tno = "'" . $tno . "'";
        $tacct = array(
            $usr, $tname, $tno, "'T'", "'0'", "'0'", "'0'", "'0'", "''", "'0'", 
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
    $sql = "SELECT * FROM `Budgets` WHERE `user` = :user AND `status` = 'A';";
    $stmnt = $pdo->prepare($sql);
    $stmnt->execute(["user" => $user]);
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
    } else {
        $lastpos = 0;
    }

    // get any card data already entered (if any)
    include "../utilities/getCards.php";
    $cdIds = [];
    $cdNames = [];
    $cdTypes = [];
    $cds = "SELECT * FROM `Cards` WHERE `user` = :user;";
    $cddat = $pdo->prepare($cds);
    $cddat->execute(["user" => $user]);
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
    $exp = "SELECT * FROM `Charges` WHERE `user` = :user;";
    $expdat = $pdo->prepare($exp);
    $expdat->execute(["user" => $user]);
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>New Account Data</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Rolling 4-month budget tracker" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <link href="../styles/standards.css" type="text/css" rel="stylesheet" />
    <link href="../styles/newBudget.css" type="text/css" rel="stylesheet" />
</head>

<body>
<p id="user" style="display:none;"><?= $user;?></p>
<p id="pnl" style="display:none;"><?= $pnl;?></p>
<div id="intro">
    <p id="ready" class="LargeHeading">You're Ready To Start!</p>
    <h2>
        Click on these three simple steps (in order) to get started:
        <span>
            <button id="done">Done Entering Data</button>
        </span>
    </h2>
    
    <div id="one" class="steps">Create the basic budget</div>
        <div id="budget">
            <span class="note NormalHeading">Note: If you make changes,
                be sure to 'Save All'</span><br />
            <form id="form" action="saveNewBudget.php" method="POST">
                <input type="hidden" name="user" value="<?= $user;?>" />
                <input type="hidden" name="lastpos" value="<?= $lastpos;?>" />
                <button id="save">Save All</button> (Changes and New Data)<br />
                    <span id="selnote">Note: When you select "Save All",
                        the data you entered (and any edits) will be 
                    saved, and new entries will be available.</span><br />
                <div id="new">
                    <span class="NormalHeading">Enter your new budget information
                        below.</span><br />
                    <div id="buditems">
                        Budget Item: <input class="acctname" type="text" 
                        name="acctname[]" />
                        Monthly Budget: <input class="bud" type="text" name="bud[]"/>
                        Current value: <input class="bal" type="text" name="bal[]" />
                        <br /><br />
                        Budget Item: <input class="acctname" type="text"
                            name="acctname[]" />
                        Monthly Budget: <input class="bud" type="text" name="bud[]"/>
                        Current value: <input class="bal" type="text" name="bal[]" />
                        <br /><br />
                        Budget Item: <input class="acctname" type="text"
                            name="acctname[]" />
                        Monthly Budget: <input class="bud" type="text" name="bud[]"/>
                        Current value: <input class="bal" type="text" name="bal[]" />
                        <br /><br />
                        Budget Item: <input class="acctname" type="text"
                            name="acctname[]" />
                        Monthly Budget: <input class="bud" type="text" name="bud[]"/>
                        Current value: <input class="bal" type="text" name="bal[]" />
                    </div>
                    <br />
                </div>
                <div id="old">
                    <?php if ($aedata) : ?>
                        <span class="NormalHeading">You can edit the data you have 
                            currently entered:</span><br /><br />
                        <div id="entered">
                        <?php for ($j=0; $j<count($aeNames); $j++) : ?>
                        Budget Item: <textarea class="acctname"
                            name="svdname[]"><?= $aeNames[$j];?></textarea>
                        Monthly Budget: <textarea class="bud"
                            name="svdbud[]"><?= $aeBudamt[$j];?></textarea>
                        Current Value: <textarea class="bal"
                        name="svdbal[]"><?= $aeCurr[$j];?></textarea>&nbsp;&nbsp;
                        Delete: <input type="checkbox" name="remove[]"
                            value="<?= $aeIds[$j];?>"><br />
                        <input type="hidden" name="ids[]" 
                            value="<?= $aeIds[$j];?>" />
                        <?php endfor; ?>
                        </div><br />
                    <?php endif; ?>
                </div>
            </form>
        </div>
    <div id="two" class="steps">Add Credit/Debit Cards</div>
        <div id="cards">
            <span class="note NormalHeading">Note: If you make changes,
                be sure to 'Save All'</span><br />
            <form id="cdform" method="POST" action="saveNewCards.php">
                <input type="hidden" name="user" value="<?= $user;?>" />
                <button id="save">Save All</button> (Changes and New Data)<br />
                    <span id="selnote">Note: When you select "Save All",
                        the data you entered (and any edits) will be 
                        saved, and new entries will be available.</span><br /><br />
                <div id="cnew">
                <span class="NormalHeading">Enter your new card information
                        below.</span><br />
                    <?php for ($y=0; $y<4; $y++) : ?>
                    New Card Name: <input type="input" name="cname[]" />
                    Card Type: <select name="ctype[]">
                        <option value="Credit">Credit</option>
                        <option value="Debit">Debit</option>
                    </select><br /><br />
                    <?php endfor; ?>
                </div>
                <div id="cold">
                    <?php if ($aecards) : ?>
                        <span class="NormalHeading">You can edit the data you have 
                            currently entered:</span><br /><br />
                        <div id="centered">
                            <?php for ($c=0; $c<count($cdNames); $c++) : ?>
                            <p id="oc<?= $c;?>" 
                                style="display:none;"><?= $cdTypes[$c];?></p>
                            Card name: <textarea class="ocname"
                            name="svdcard[]"><?= $cdNames[$c];?></textarea>
                            Card type: <select name="svdtype[]" id="seloc<?= $c;?>">
                                <option value="Credit">Credit</option>
                                <option value="Debit">Debit</option>
                            </select>&nbsp;&nbsp;
                            Delete: <input type="checkbox" name="cdel[]" 
                                value="<?= $cdIds[$c]?>" />
                            <input type="hidden" name="cdids[]"
                                value="<?= $cdIds[$c];?>" />
                            <br />
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    <div id="three" class="steps">Enter/Edit Outstanding/Unpaid Charges</div>
        <div id="expenses">
            <span class="note NormalHeading">Note: If you make changes,
                be sure to 'Save All'</span><br />
            <form id="cdform" method="POST" action="saveNewCharges.php">
                <input type="hidden" name="user" value="<?= $user;?>" />
                <button id="save">Save All</button> (Changes and New Data)<br />
                    <span id="selnote">Note: When you select "Save All",
                        the data you entered (and any edits) will be 
                        saved, and new entries will be available.</span><br /><br />
                <div id="enew">
                    <span class="NormalHeading">Enter your new expense information
                        below. (Outstanding/unpaid charges only)</span><br />
                    <?php for ($z=0; $z<4; $z++) : ?>
                    Date Expense Entered (Use: yyyy-mm-dd) <input type="input"
                        name="edate[]" /><br />
                    Credit Card Used:
                    <span id="ncd<?= $z;?>"><?= $ccHtml;?></span>&nbsp;&nbsp;
                    Amount Paid: <input type="text" name="eamt[]" />&nbsp;&nbsp;
                    Payee: <input type="text" name="epay[]" /></span><br /><br />
                    <?php endfor; ?>
                </div>
                <div id="eold">
                    <?php if ($aeexp) : ?>
                    <span class="NormalHeading">You can edit the data you have 
                        currently entered:</span><br /><br />
                    <div id="eentered">
                        <?php for ($e=0; $e<count($exIds); $e++) : ?>
                        <p id="cd<?= $e;?>" 
                            style="display:none;"><?= $aeCard[$e];?></p>
                        Date Entered: <textarea class="exp dates"
                            name="aeedate[]"><?= $aeDate[$e];?></textarea>
                        Credit Card Used:
                        <span id="crcd<?= $e?>"><?= $ccHtml;?></span>
                        Amount Paid: <textarea class="exp amts"
                            name="aeeamt[]"><?= $aeAmt[$e];?></textarea>
                        Payee: <textarea class="exp"
                            name="aeepay[]"><?= $aePayee[$e];?></textarea>
                        Delete: <input type="checkbox" name="edel[]" 
                            value="<?= $exIds[$e]?>" />
                        <input type="hidden" name="expids[]"
                            value="<?= $exIds[$e];?>" />
                        <br />
                        <?php endfor; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>
<!-- end of data entry -->
</div>

<script src="../scripts/jquery-1.12.1.js" type="text/javascript"></script>
<script src="../scripts/newBudget.js" type="text/javascript"></script>

</body>

</html>
