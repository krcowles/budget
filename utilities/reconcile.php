<?php
/**
 * This module allows the user to reconcile a credit card statement against
 * the chosen credit card.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
$user = filter_input(INPUT_GET, 'user'); // mandatory to appear prior to 'requires'
$rec_cd = filter_input(INPUT_GET, 'card');

require "getCards.php";
require "getExpenses.php";
 // get expenses for the chosen card:
$card_data = [];
for ($i=0; $i<count($expamt); $i++) {
    if ($expcdname[$i] === $rec_cd) {
        $info = array('date' => $expdate[$i], 'amt' => $expamt[$i],
            'acct' => $expcharged[$i], 'payee' => $exppayee[$i],
            'tblid' => $expid[$i]);
        array_push($card_data, $info);
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Reconcile Monthly Statement</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Credit card statement reconciliation" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <link href="../styles/standards.css" type="text/css" rel="stylesheet" />
    <link href="../styles/charges.css" type="text/css" rel="stylesheet" />
    <link href="../styles/reconcile.css" type="text/css" rel="stylesheet" />
</head>

<body>

<div style="margin-left:16px;" id="container">
    <p class="NormalHeading">This form will allow you to reconcile  
        your "<?= $rec_cd;?>" card against your monthly statement.
        Your budget will be automatically updated to show payment of those
        charges.</p>
    <form id="form" method="POST" action="saveReconciledCharges.php">
        <button id="reconcile">Reconcile</button>
        <button id="rtb">Return to Budget</button><br /><br />
        <span id="inst">Check the items to be reconciled; when done, select
            'Reconcile' above. These items will be automatically deducted
            from your checkbook balance as if the charges have been paid.<br /><br />
        </span>
        <input type="hidden" name="user" value="<?= $user;?>" />
        <input type="hidden" name="card" value="<?= $rec_cd;?>" />
        <table>
            <thead>
                <tr>
                    <th>Date:</th>
                    <th>Amount</th>
                    <th>Deducted From:</th>
                    <th>Payee:</th>
                </tr>
            </thead>
            <?php if (count($card_data) === 0) : ?>
                <p>All charges on this card have been reconciled</p>
            <?php else : ?>
            <tbody>
                <?php for($j=0; $j<count($card_data); $j++) : ?>
                <tr>
                    <td class="chgdate"><?= $card_data[$j]['date'];?></td>
                    <td class="right chgamt"><?= $card_data[$j]['amt'];?></td>
                    <td class="left cgto"><?= $card_data[$j]['acct'];?></td>
                    <td class="left chgpayee"><?= $card_data[$j]['payee'];?></td>
                    <td><input type="checkbox" name="del[]" id="chg<?= $j;?>"
                        value="<?= $card_data[$j]['tblid'];?>" /></td>
                </tr>
                <?php endfor; ?>
            </tbody>
            <?php endif; ?>
        </table><br />
    </form>
</div>

<script src="../scripts/jquery-1.12.1.js" type="text/javascript"></script>
<script src="../scripts/reconcile.js" type="text/javascript"></script>
</body>

</html>
