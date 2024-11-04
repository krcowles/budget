<?php
/**
 * This module allows the user to reconcile a credit card statement against
 * the chosen credit card.
 * PHP Version 7.3
 *
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
$rec_cd = filter_input(INPUT_GET, 'card');

require "getCards.php";
require "getExpenses.php";
require "timeSetup.php";
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
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reconcile Monthly Statement</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Credit card statement reconciliation" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="../styles/bootstrap.min.css" type="text/css" rel="stylesheet" />
    <link href="../styles/modals.css" type="text/css" rel="stylesheet" />
    <link href="../styles/charges.css" type="text/css" rel="stylesheet" />
    <link href="../styles/reconcile.css" type="text/css" rel="stylesheet" />
</head>

<body>
<?php require "../main/navbar.php"; ?>

<div style="margin-left:16px;" id="container">
    <br />
    <h4 class="NormalHeading">This form will allow you to reconcile  
        your "<?= $rec_cd;?>" card against your monthly statement.
        Your budget will be automatically updated to show payment of those
        charges.</h4>
    <form id="form" method="post" action="saveReconciledCharges.php">
        <div>
            <button id="reconcile_chgs" type="button" class="btn btn-secondary">
            Reconcile</button><br /><br />
            <input type="hidden" name="card" value="<?= $rec_cd;?>" />
            <table class="sortable">
                <thead>
                    <tr>
                        <th data-sort="std">Date:</th>
                        <th data-sort="amt">Amount</th>
                        <th data-sort="std">Deducted From:</th>
                        <th data-sort="std">Payee:</th>
                        <th></th>
                    </tr>
                </thead>
                <?php if (count($card_data) === 0) : ?>
                    <p>All charges on this card have been reconciled</p>
                <?php else : ?>
                <tbody>
                    <?php for ($j=0; $j<count($card_data); $j++) : ?>
                    <tr>
                        <td class="chgdate"><?= $card_data[$j]['date'];?></td>
                        <td class="right chgamt"><?= $card_data[$j]['amt'];?></td>
                        <td class="left cgto"><?= $card_data[$j]['acct'];?></td>
                        <td class="left chgpayee"><?= $card_data[$j]['payee'];?></td>
                        <td><input type="checkbox" name="del[]" id="chg<?=$j;?>"
                            value="<?= $card_data[$j]['tblid'];?>" /></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
                <?php endif; ?>
            </table><br />
        </div>
    </form>
    <div id="rsum">
        <span class="rsum_hdr">Current sum of reconciled charges:</span><br />
        <span class="sum">$ <span id="sumtxt"></span></span><br />
        <span class="rsum_hdr">Unreconciled:</span><br />
        <span class="sum">$ <span id="rectxt"></span></span>
    </div>
</div>

<?php require_once "../main/bootstrapModals.php"; ?>

<script src="https://unpkg.com/@popperjs/core@2.4/dist/umd/popper.min.js"></script>
<script src="../scripts/bootstrap.min.js"></script>
<script src="../scripts/jquery.min.js"></script>
<script src="../scripts/menus.js"></script>
<script src="../scripts/tableSort.js"></script>
<script src="../scripts/reconcile.js"></script>
</body>

</html>
