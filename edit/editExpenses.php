<?php
/**
 * This allows the user to modify expense data in the `Charges` table
 * for expenses already paid in the last 30 days.
 * PHP Version 7.1
 * 
 * @package BUDGET
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require "../utilities/getAccountData.php";
require "../utilities/timeSetup.php";

// add 'blank' option to $fullsel:
$optloc = strpos($fullsel, "<option");
$backhalf = substr($fullsel, $optloc);
$newsel = '<select class="fullsel" name="chgd[]"><option value="">' .
    'SELECT Account Charged:</option>';
$newsel .= $backhalf;

$prev30 = time() - (30 * 24 * 60 * 60);
// arrays holding data
$expid  = [];
$exptyp = [];
$expcrd = [];
$expamt = [];
$expdte = [];
$exppye = [];
$expact = [];

$expreq = "SELECT * FROM `Charges` WHERE `userid` = :uid AND `method` <> 'Credit';";
$data = $pdo->prepare($expreq);
$data->execute(["uid" => $_SESSION['userid']]);
$expdat = $data->fetchALL(PDO::FETCH_ASSOC);
foreach ($expdat as $expense) {
    $rel = strtotime($expense['expdate']);
    if ($rel >= $prev30) {
        array_push($expid,  $expense['expid']);
        array_push($exptyp, $expense['method']);
        array_push($expcrd, $expense['cdname']);
        array_push($expdte, $expense['expdate']);
        array_push($expamt, $expense['expamt']);
        array_push($exppye, $expense['payee']);
        array_push($expact, $expense['acctchgd']);
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Edit Expenses Within 30 Days</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Rolling 3-month budget tracker" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <link href="../styles/standards.css" type="text/css" rel="stylesheet" />
    <link href="../styles/jquery-ui.css" type="text/css" rel="stylesheet" />
    <style type="text/css">
        textarea { height: 24px; font-size: 16px; padding-top: 4px; }
        .type { width: 100px; }
        .name { width: 130px; }
        .dates { width: 120px; height: 22px; font-size: 16px; }
        .amt { width: 100px; }
        
        #main { margin-left: 24px; }
    </style>
</head>

<body>

<div id="main">
    <p class="NormalHeading">You can use this form to edit any expense paid 
    within the last 30 days. <strong style="color:brown;">NOTE:</strong> Changes
    to dollar amounts will be reflected in the associated accounts</p>
    <form id="form" method="post" action="saveEditedExpenses.php">
    <div>
        <button id="save">Save All Changes</button>
        <button id="return" style="margin-left:80px;">Return to Budget</button>
        <button id="viewer" style="margin-left:80px;">Return to View/Manage</button>
        <br /><br />
        <table>
            <thead>
                <tr>
                    <th>Type:</th>
                    <th>[Debit Card]</th>
                    <th>Date:</th>
                    <th>Amount</th>
                    <th>Payee:</th>
                    <th>Deducted From:</th>
                    <th style="visibility:hidden;"></th>
                    <th style="visibility:hidden;"></th>
                    <th style="visibility:hidden;"></th>
                </tr>
            </thead>
            <tbody>
                <?php for ($i=0; $i<count($expid); $i++) : ?>
                <tr>
                    <td><textarea rows="1" cols="12" class="type" 
                        name="type[]"><?= $exptyp[$i];?></textarea></td>
                    <td><textarea rows="1" cols="15" class="name"
                        name="cdname[]"><?= $expcrd[$i];?></textarea></td>
                    <td><input type="text" class="datepicker dates"
                        name="date[]" value="<?= $expdte[$i];?>" /></td>
                    <td><textarea rows="1" cols="14" class="amt"
                        name="amt[]"><?= $expamt[$i];?></textarea></td>
                    <td><textarea  rows="1" cols="30" class="payee"
                        name="pay[]"><?= $exppye[$i];?></textarea></td>
                    <td><?= $newsel;?></td>
                    <td><input type="hidden" name="exid[]"
                        value ="<?= $expid[$i];?>" /></td>
                    <td><input type="hidden" name="org[]"
                        value="<?= $expamt[$i];?>" /></td>
                    <td style="display:none;">
                        <span id="acct<?= $i;?>"><?= $expact[$i];?></span></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table><br />
    </div>
    </form>
    <p style="clear:left;margin-left:16px;">
        <a href="http://validator.w3.org/check?uri=referer">
            <img src="http://www.w3.org/Icons/valid-xhtml10"
            alt="Valid XHTML 1.0 Strict" height="31" width="88" />
        </a>
    </p>
</div>

<script src="../scripts/jquery-1.12.1.js" type="text/javascript"></script>
<script src="../scripts/jquery-ui.js" type="text/javascript"></script>
<script src="../scripts/dbValidation.js" type="text/javascript"></script>
<script src="../scripts/editExpenses.js" type="text/javascript"></script>

</body>
</html>