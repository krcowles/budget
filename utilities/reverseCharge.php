<?php
/**
 * This script will present the user with a list of credit card charges
 * from which to select. When the selection(s) is/are executed, the charges
 * will be 'reversed' so that they are removed from the credit card charge
 * list and refunded to the charged account.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require_once "../database/global_boot.php";

$user = filter_input(INPUT_GET, 'user');
$paid = isset($_GET['paid']) ? true : false;

$chargeRequest = "SELECT * FROM `Charges` WHERE " .
    "`user` = :user AND `paid` = 'N' AND `method` = 'Credit';";
$charge = $pdo->prepare($chargeRequest);
$charge->execute(["user" => $user]);
$charges = $charge->fetchAll(PDO::FETCH_ASSOC);
$noOfCards = 0;
$cards = [];
$chgs  = [];
if ($charges) {
    // Determine the number of cards to be able to sort according to card name
    foreach ($charges as $entry) {
        if (!in_array($entry['cdname'], $cards)) {
            array_push($cards, $entry['cdname']);
        }
    }
    $noOfCards = count($cards);
    // Now form arrays for each card
    for ($i=0; $i<$noOfCards; $i++) {
        $card_data = [];
        foreach ($charges as $item) {
            if ($item['cdname'] === $cards[$i]) {
                $user_item = array(
                    $item['expid'],
                    $item['expamt'],
                    $item['acctchgd'],
                    $item['expdate']
                );
                array_push($card_data, $user_item);
            }
        }
        array_push($chgs, $card_data);
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Reverse Charge</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Reverse one or more credit card charges" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <link href="../styles/standards.css" type="text/css" rel="stylesheet" />
    <link href="../styles/reverseCharge.css" type="text/css" rel="stylesheet" />
 
</head>
<body>
<h2>Select one or more cards whose charges you wish to reverse.</h2>
<h3>This will have the effect of removing the charge from the card's accumulated
expense, and having the charge placed back into the account from which it was 
originally drawn</h3>
<?php if ($paid) : ?>
<h3 id="paid">Charge(s) Successfully Completed</h3>
<? endif; ?>
<form action="doReverse.php" method="POST">
    <button>Reverse Charges</button>
    <button id="return">Return To Budget</button>
    <input type="hidden" name="user" value="<?= $user;?>" />
<?php if ($noOfCards === 0) : ?>
    <h3>You have no outstanding credit card charges</h3>
<?php else : ?>
    <div id="main">
    <?php for ($j=0; $j<$noOfCards; $j++) : ?>
        <h3>For Credit Card <?= $cards[$j];?>:</h3>
        <div id="carddiv">
            <?php for ($k=0; $k<count($chgs[$j]); $k++) : ?>
            <div id="<?= $chgs[$j][$k][0];?>" style="margin-bottom:6px;">
                <input type="hidden" name="card[]" value="<?= $cards[$j];?>" />
                <input type="checkbox" name="revchg[]"
                    value="<?= $chgs[$j][$k][0];?>" /> &nbsp;&nbsp;
                <input class="cdentry amts" type="text"
                    name="amt<?= $chgs[$j][$k][0];?>"
                    value="<?= $chgs[$j][$k][1];?>" />
                <input class="cdentry dates" type="text"
                    value="<?= $chgs[$j][$k][3];?>" />
                <input class="cdentry accts" type="text"
                    name="acc<?= $chgs[$j][$k][0];?>"
                    value="<?= $chgs[$j][$k][2];?>" />
            </div>
            <?php endfor; ?>
        </div>
    <?php endfor; ?>
    </div>
<?php endif; ?>
</form>

<script src="../scripts/jquery-1.12.1.js" type="text/javascript"></script>
<script src="../scripts/reverseCharge.js" type="text/javascript"></script>
</body>

</html>