<?php
/**
 * This allows the user to modify expense data in the `Charges` table.
 * PHP Version 7.1
 * 
 * @package BUDGET
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
$user = filter_input(INPUT_GET, 'user');
require "../utilities/getCards.php";
require "../utilities/getExpenses.php";

$cardCharges = [];
$allCharges = [];
$card_cnts = [];
for ($i=0; $i<count($cr); $i++) {
    for ($j=0; $j<count($expmethod); $j++) {
        if ($expmethod[$j] === 'Credit' && $expcdname[$j] === $cr[$i]) {
                $cardCharges[] = array(
                    'date' => $expdate[$j], 'amt' => $expamt[$j],
                    'chgd' => $expcharged[$j], 'payee' => $exppayee[$j]
                );
        }
    }
    $allCharges[$i] =  $cardCharges;
    $cardCharges = [];
    $card_cnts[$i] = count($allCharges[$i]);
}
//print_r($allCharges);
$x=1;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Edit Credit Active Expenses</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Rolling 3-month budget tracker" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <link href="../styles/standards.css" type="text/css" rel="stylesheet" />
    <link href="../styles/charges.css" type="text/css" rel="stylesheet" />
    <style type="text/css">
        textarea { height: 24px; font-size: 16px; padding-top: 4px; }
        .dates { width: 120px; }
        .amt { width: 100px;}
        #main { margin-left: 24px; }
        .left { text-align: left }
        .right { text-align: right }
    </style>
</head>

<body>
<div id="main">
    <p id="user" style="display:none;"><?= $user;?></p>
    <p class="NormalHeading">You can use this form to edit active charges
    charged to a credit card.</p>
    <form id=#form method="POST" action="saveEditedCharge.php">
    <button id="save">Save All Changes</button>
    <button id="return" style="margin-left:80px;">
        Return to Budget</button><br /><br />
    <input type="hidden" name="user" value="<?= $user;?>" />
    <div id="existing">
    <?php for ($i=0; $i<count($cr); $i++) : ?>
        <input type="hidden" name="cnt[]" value="<?= $card_cnts[$i];?>" />
        <span class="BoldText">These are your current charges against
            <?= $cr[$i];?>
        </span>
        <table>
            <thead>
                <tr>
                    <th>Date:</th>
                    <th>Amount</th>
                    <th>Deducted From:</th>
                    <th>Payee:</th>
                </tr>
            </thead>
            <tbody>
                    <?php foreach ($allCharges[$i] as $card) : ?>
                    <tr>
                        <td><textarea name="cr<?= $i;?>date[]"
                            class="dates"><?= $card['date'];?></textarea>
                        </td>
                        <td><textarea class="amt"
                            name="cr<?= $i;?>amt[]"><?= $card['amt'];?>
                        </textarea></td>
                        <td><textarea class="chgd"
                            name="cr<?=$i;?>chgd[]"><?= $card['chgd'];?>
                        </textarea></td>
                        <td><textarea  class="payee"
                        name="cr<?= $i;?>pay[]"><?= $card['payee'];?>
                        </textarea></td>
                    </tr>
                    <?php endforeach; ?>
            </tbody>
        </table><br />
    <?php endfor; ?>
    </div>
    </form>
</div>

<script src="../scripts/jquery-1.12.1.js" type="text/javascript"></script>
<script type="text/javascript">
    $('#return').on('click', function(ev) {
        ev.preventDefault();
        var dpg = "../main/displayBudget.php?user=" + $('#user').text();
        window.open(dpg, "_self");
    });
</script>
</body>

</html>