<?php
/**
 * This utility queries the database for all the specified user's outstanding 
 * credit card charges, as well as the paid checks/drafts from the previous
 * 30 days, and displays them for viewing only.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();

require_once "getAccountData.php";
require_once "getCards.php";
require_once "getExpenses.php";
require_once "get30DayExpenses.php";
$btnclick = isset($_GET['click']) ? filter_input(INPUT_GET, 'click') : 'none';
/**
 * The table bodies are created in php below, owing to an original attempt
 * to use XHTML strict. This is no longer the case, but the code remains.
 */
// setup data for credit cards
$counts = [];
$tbodys = [];
foreach ($cr as $credit_acct) {
    $tally = 0;
    $tbody = '<tbody>' . PHP_EOL;
    for ($k=0; $k<count($expamt); $k++) {
        if ($credit_acct == $expcdname[$k]) {
            $tally++;
            $tbody .= '<tr>' . PHP_EOL;
            $tbody .= "<td class='col1'>{$expdate[$k]}</td>" . PHP_EOL;
            $tbody .= "<td class='col2'>{$expamt[$k]}</td>" . PHP_EOL;
            $tbody .= "<td class='col3'>{$exppayee[$k]}</td>" . PHP_EOL;
            $tbody .= "<td class='col4'>{$expcharged[$k]}</td>" . PHP_EOL;
            $tbody .= "<td class='mvcr'><input id='cr{$expid[$k]}' " .
                "type='checkbox' /></td>" . PHP_EOL;
            $tbody .= '</tr>' . PHP_EOL;
        }
    }
    $tbody .= '</tbody>' . PHP_EOL;
    if ($tally === 0) {
        $tbody = '<tbody><tr><td colspan="5"></td></tr></tbody>';
    }
    array_push($counts, $tally);
    array_push($tbodys, $tbody);
}
// expenses
if (count($amt) ===0) {
    $ebody = '<tbody><tr><td colspan="5"></td></tr></tbody>';
} else {
    $ebody = '<tbody>' . PHP_EOL;
    for ($m=0; $m<count($amt); $m++) {
        $ebody .= '<tr>' . PHP_EOL;
        $ebody .= "<td class='col1'>{$dte[$m]}</td>" . PHP_EOL;
        $ebody .= "<td class='col2'>{$amt[$m]}</td>" . PHP_EOL;
        $ebody .= "<td class='col3'>{$pye[$m]}</td>" . PHP_EOL;
        $ebody .= "<td class='col4'>{$ded[$m]}</td>" . PHP_EOL;
        $ebody .= "<td class='mvdr'><input id='dr{$eid[$m]}' " .
            'type="checkbox" /></td>' . PHP_EOL;
        $ebody .= '</tr>' . PHP_EOL;
    }
    $ebody .= '</tbody>' . PHP_EOL;
}
// debits
$dcounts = [];
$dbodys = [];
for ($p=0; $p<count($dr); $p++) {
    $tally = 0;
    $dbody = '<tbody>' . PHP_EOL;
    for ($q=0; $q<count($damt); $q++) {
        if ($debname[$q] == $dr[$p]) {
            $tally++;
            $dbody .= '<tr>' . PHP_EOL;
            $dbody .= "<td class='col1'>{$dday[$q]}</td>" . PHP_EOL;
            $dbody .= "<td class='col2'>{$damt[$q]}</td>" . PHP_EOL;
            $dbody .= "<td class='col3'>{$dpay[$q]}</td>" . PHP_EOL;
            $dbody .= "<td class='col4'>{$dfrm[$q]}</td>" . PHP_EOL;
            $dbody .= "<td class='mvdr'><input id='dr{$did[$q]}' " .
                "type='checkbox' /></td>" . PHP_EOL;
            $dbody .= "</tr>" . PHP_EOL;
        }
    }
    $dbody .= '</tbody>' . PHP_EOL;
    if ($tally ===0) {
        $dbody = '<tbody><tr><td colspan="5"></td></tr></tbody>';
    }
    array_push($dcounts, $tally);
    array_push($dbodys, $dbody);
}
?>
<!DOCTYPE html>

<html lang="en">
<head>
    <title>Current Outstanding Charges</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Rolling 3-month budget tracker" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="../styles/bootstrap.min.css" type="text/css" rel="stylesheet" />
    <link href="../styles/modals.css" type="text/css" rel="stylesheet"/>
    <link href="../styles/manageExp.css" type="text/css" rel="stylesheet" />
</head>

<body>
<?php require_once "../main/navbar.php"; ?>
<p id="btn2click" style="display:none"><?=$btnclick;?></p>
<br />
<h3 class="hdr">The following actions may be applied to the expenses below.
    Note that the unpaid credit card charges are on the left; the debit card and
    check/draft expenses already paid are on the right.
</h3>
<div class="hdr">
<h5 class="actions">Update/Edit your data:</h5>
<button id="edcr" type="button" class="btn btn-secondary">Edit</button>
    <span class="edtxt">&nbsp;&nbsp;You can edit any of your Credit Charges
    (a separate page will appear)</span><br />
<button id="edexp" type="button" class="btn btn-secondary">Update</button>
    <span class="edtxt">&nbsp;&nbsp; You can update any expenses already paid
    (a separate page will appear)</span><br /><br />

<h5 class="actions">Move items from one place to another:</h5>
<button id="e2c" type="button" class="btn btn-secondary">Move Expense</button>
    <span id="mve2c" class="edtxt">&nbsp;&nbsp;
        Move a paid expense to an unpaid Credit Card charge&nbsp;&nbsp;</span><br />
<button id="c2c" type="button" class="btn btn-secondary">Swap</button>
    <span id="mvc2c" class="edtxt">&nbsp;&nbsp;
        Move a charge from one credit card to another&nbsp;&nbsp;</span><br />
<button id="c2e" type="button" class="btn btn-secondary">Move Charge</button>
    <span id="mvc2e" class="edtxt">&nbsp;&nbsp;
        Move an unpaid Credit Card charge to a paid expense&nbsp;&nbsp;</span>
</div>

<button id="canc" type="button"
    class="btn btn-danger">Cancel This Transfer</button>

<div id="ochgs">
    <h4>The following entries display the
        current/outstanding charges to your <em style="color:darkgreen;">credit
        card(s)</em>.
    </h4>
    <hr id="barloc" />


<?php for ($i=0; $i<count($cr); $i++) : ?>
    <span>For credit card <em style="color:brown;">
        <?=$cr[$i];?></em></span> :&nbsp;&nbsp;&nbsp;
    <span class="mvtocr"><strong class="disptocr">To:</strong> 
        <input id="tocr<?= $i;?>" type="checkbox" /></span><br />
    <table class="crtbl sortable">
        <thead>
            <tr>
                <th data-sort="std" class="ascending">Date Incurred</th>
                <th data-sort="amt">Amount</th>
                <th data-sort="std">Payee</th>
                <th data-sort="std">Deducted From</th>
                <th class="mvcr hdcr">Mv</th>
            </tr>
        </thead>
        <?=$tbodys[$i];?>
    </table>
    <hr />
<?php endfor; ?>
</div>

<div id="exps">
    <h4 style="padding-left:4px;">The following entries display paid
        <em style="color:darkgreen;">check,
        draft, or debit card</em> expenses for the previous 30-day period.
    </h4>
    <hr id="barloc" />
    <span>Checks/Drafts :</span>
    <div id="innerexp">
        <span class="mvtodr"><strong class="disptodr">To:</strong> 
        <input id="todrcheck" type="checkbox" /></span>
        <table class="drtbl sortable">
            <thead>
                <tr>
                    <th data-sort="amt" class="ascending">Date Incurred</th>
                    <th data-sort="amt">Amount</th>
                    <th data-sort="std">Payee</th>
                    <th data-sort="std">Deducted From</th>
                    <th class="mvdr hddr">Mv</th>
                </tr>
            </thead>
            <?=$ebody;?>
        </table>
        <hr />

        <?php for ($i=0; $i<count($dr); $i++) : ?>
            <span class="SmallHeading">For debit card <em style="color:brown;">
            <?=$dr[$i];?></em> :</span>&nbsp;&nbsp;&nbsp;
            <span class="mvtodr"><strong class="disptodr">To:</strong> 
            <input id="todr<?= $i;?>" type="checkbox" /></span><br />
            <table class="drtbl sortable">
                <thead>
                    <tr>
                        <th data-sort="amt" class="ascending">Date Incurred</th>
                        <th data-sort="amt">Amount</th>
                        <th data-sort="std">Payee</th>
                        <th data-sort="std">Deducted From</th>
                        <th class="mvdr hddr">Mv</th>
                    </tr>
                </thead>
                <?=$dbodys[$i];?>
            </table>
            <hr />
        <?php endfor; ?>
    </div>
</div>

<?php require_once "../main/bootstrapModals.html"; ?>

<script src="https://unpkg.com/@popperjs/core@2.4/dist/umd/popper.min.js"></script>
<script src="../scripts/bootstrap.min.js"></script>
<script src="../scripts/jquery-1.12.1.js"></script>
<script src="../scripts/menus.js"></script>
<script src="../scripts/tableSort.js"></script>
<script src="../scripts/manageExp.js"></script>

</body>
</html>
