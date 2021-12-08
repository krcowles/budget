<?php
/**
 * This allows the user to modify expense data in the `Charges` table
 * for expenses already paid in the last 30 days.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require "../utilities/getAccountData.php";
require "../utilities/timeSetup.php";
require "../utilities/getCards.php";

// add 'blank' option to $fullsel:
$optloc = strpos($fullsel, "<option");
$backhalf = substr($fullsel, $optloc);
$newsel = '<select class="fullsel" name="chgd[]"><option value="">' .
    'SELECT Account Charged:</option>';
$newsel .= $backhalf;

$end_date = date("Y-m-d", time());
$prev30 = time() - (30 * 24 * 60 * 60);
$str_date = date("Y-m-d", $prev30);

// arrays holding data
$expid  = [];
$expmth = [];
$expcrd = [];
$expamt = [];
$expdte = [];
$exppye = [];
$expact = [];

// the following variable will hold data for any 'Debit' cards or 'Check's
$expreq = "SELECT * FROM `Charges` WHERE `userid` = :uid AND `method` <> 'Credit' " .
    "AND `expdate` BETWEEN '{$str_date}' AND '{$end_date}';";
$data = $pdo->prepare($expreq);
$data->execute(["uid" => $_SESSION['userid']]);
$expdat = $data->fetchALL(PDO::FETCH_ASSOC);
foreach ($expdat as $expense) {
    $rel = strtotime($expense['expdate']);
    if ($rel >= $prev30) {
        array_push($expid,  $expense['expid']);
        array_push($expmth, $expense['method']);
        array_push($expcrd, $expense['cdname']);
        array_push($expdte, $expense['expdate']);
        array_push($expamt, $expense['expamt']);
        array_push($exppye, $expense['payee']);
        array_push($expact, $expense['acctchgd']);
    }
}
$paymethod = '<select class="meths" name="meths[]"><option value="Check" selected>' .
    'Check or Draft</option><option value="Debit">Debit Card</option>' .
    '</select>';
$drcards = '<select class="drcrds" name="drcrds[]"><option value="Check" selected>' .
    'N/A</option>';
for ($k=0; $k<count($dr); $k++) {
    $drcards .= '<option value="' . $dr[$k] . '">' . $dr[$k] . '</option>';
}
$drcards .= '</select>' . PHP_EOL;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Expenses Within 30 Days</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Rolling 3-month budget tracker" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="../styles/bootstrap.min.css" type="text/css" rel="stylesheet" />
    <link href="../styles/editExpenses.css" type="text/css" rel="stylesheet" />
    <link href="../styles/jquery-ui.css" type="text/css" rel="stylesheet" />
</head>

<body>
<?php require "../main/navbar.php"; ?>
<div id="main">
    <br />
    <h4>You can use this form to edit any expense paid within the last 30 days.</h4>
    <h5><strong style="color:brown;">NOTE:</strong> All changes made here will be
        reflected in the associated accounts. Changing 'Amount' expensed will affect
        your checkbook balance.</h5>
    <form id="form" method="post" action="saveEditedExpenses.php">
    <div>
        <button id="save" type="button" class="btn btn-secondary">
            Save All Changes</button>
        <br /><br />
        <table>
            <thead>
                <tr>
                    <th>Pay Method</th>
                    <th>Card Name:</th>
                    <th>Date:</th>
                    <th>Amount</th>
                    <th>Payee:</th>
                    <th>Deducted From:</th>
                    <th style="visibility:hidden;"></th>
                    <th style="visibility:hidden;"></th>
                    <th style="visibility:hidden;"></th>
                    <th style="visibility:hidden;"></th>
                    <th style="visibility:hidden;"></th>
                </tr>
            </thead>
            <tbody>
                <?php for ($i=0; $i<count($expid); $i++) : ?>
                <tr>
                    <td><?=$paymethod;?></td>
                    <td><?=$drcards;?></td>
                    <td><input type="text" class="datepicker dates"
                        name="date[]" value="<?= $expdte[$i];?>" /></td>
                    <td><textarea class="amt"
                        name="amt[]"><?= $expamt[$i];?></textarea></td>
                    <td><textarea  class="payee"
                        name="pay[]"><?= $exppye[$i];?></textarea></td>
                    <td><?=$newsel;?></td>
                    <!-- hidden data -->
                    <td><input type="hidden" name="exid[]"
                        value ="<?= $expid[$i];?>" /></td>
                    <td><input type="hidden" name="org[]"
                        value="<?=$expamt[$i];?>" /></td>
                    <td><input type="hidden" name="oact[]"
                        value="<?=$expact[$i];?>" /></td>
                    <td class="hidden"><?=$expmth[$i];?></td>
                    <td class="hidden"><?=$expcrd[$i];?></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table><br />
    </div>
    </form>
</div>

<?php require_once "../main/bootstrapModals.html"; ?>

<script src="https://unpkg.com/@popperjs/core@2.4/dist/umd/popper.min.js"></script>
<script src="../scripts/bootstrap.min.js"></script>
<script src="../scripts/jquery.min.js"></script>
<script src="../scripts/jquery-ui.js"></script>
<script src="../scripts/menus.js"></script>
<script src="../scripts/editExpenses.js"></script>
<script src="../scripts/dbValidation.js"></script>

</body>
</html>