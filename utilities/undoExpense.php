<?php
/**
 * This script will present the user with a list of expenses from the
 * last 30 days and he/she may select one or more to be 'undone'.
 * When the selection(s) is/are executed, the expense item(s) will be zeroed
 * and the charge(s) will be refunded to the originally charged account(s).
 * PHP Version 7.1
 *
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();

require_once "../database/global_boot.php";
require_once "get30DayExpenses.php";
$paid_exp = isset($_GET['paid']) ? true : false;

$chgs = [];
for ($j=0; $j<count($eid); $j++) {
    $user_data = array(
        $eid[$j],
        "Check/Draft",
        $dte[$j],
        $amt[$j],
        $ded[$j],
        $pye[$j]
    );
    array_push($chgs, $user_data);
}
for ($k=0; $k<count($did); $k++) {
    $user_data = array(
        $did[$k],
        "Debit: " . $debname[$k],
        $dday[$k],
        $damt[$k],
        $dfrm[$k],
        $dpay[$k]
    );
    array_push($chgs, $user_data);
}
$noOfExp = count($chgs);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Undo Expense</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Undo one or more expense charges" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="../styles/bootstrap.min.css" type="text/css" rel="stylesheet" />
    <link href="../styles/reverseCharge.css" type="text/css" rel="stylesheet" />
    <style type="text/css">
        tbody tr:nth-child(odd):hover {background-color: #bfd9bf;}
        tbody tr:nth-child(even):hover {background-color: #bfd9bf;}
    </style>
</head>
<body>
<?php require "../main/navbar.php"; ?>
<p id="count" style="display:none;"><?=$noOfExp;?></p>
<div style="margin-left:40px;">
    <br />
    <h4>Select one or more expenses you wish to undo.</h4>
    <h5>This will have the effect of deleting the expense and having the deleted 
        amount placed back into the account from which it was originally drawn.
        This will increase your 'Checkbook Total' by the same amount.</h5>
    <?php if ($paid_exp) : ?>
    <h4 id="paid">Expense(s) Successfully Undone</h4>
    <?php endif; ?>
    <div>
        <div>
            <button id="revexp" class="btn btn-secondary" type="button">
                Undo Expense</button>&nbsp;&nbsp;<span id="action">All checked
                    boxes will have their respective paid expenses reversed</span>
        </div><br />
        <?php if ($noOfExp === 0) : ?>
        <h4>You have no outstanding expenses for the last 30 days</h4>
        <?php else : ?>
        <div id="main">
            <h4>Expenses From the Last 30 Days</h4>
            <h5>Click on a header to sort; again to reverse</h5>
            <table class="sortable">
                <thead>
                    <tr>
                        <th>Undo</th>
                        <th data-sort="std">Debit Type</th>
                        <th data-sort="std">Date</th>
                        <th data-sort="amt">Amount</th>
                        <th data-sort="std">Remarks</th>
                        <th data-sort="std">Account Charged</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($j=0; $j<$noOfExp; $j++) : ?>
                    <tr>
                        <td class="calign"><input type="checkbox"
                            value="<?=$chgs[$j][0];?>" /></td>
                        <td><?=$chgs[$j][1];?></td>
                        <td><?=$chgs[$j][2];?></td>
                        <td><?=$chgs[$j][3];?></td>
                        <td><?=$chgs[$j][5];?></td>
                        <td><?=$chgs[$j][4];?></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php require "../main/bootstrapModals.html"; ?>

<script src="https://unpkg.com/@popperjs/core@2.4/dist/umd/popper.min.js"></script>
<script src="../scripts/bootstrap.min.js"></script>
<script src="../scripts/jquery-1.12.1.js" type="text/javascript"></script>
<script src="../scripts/menus.js"></script>
<script src="../scripts/undoExpense.js" type="text/javascript"></script>
<script src="../scripts/tableSort.js"></script>
</body>

</html>