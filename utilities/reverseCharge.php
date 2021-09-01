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
session_start();
require_once "../database/global_boot.php";
require_once "../utilities/timeSetup.php";

$chgs_paid = isset($_GET['paid']) ? true : false;

$chargeRequest = "SELECT * FROM `Charges` WHERE " .
    "`userid` = :uid AND `paid` = 'N' AND `method` = 'Credit';";
$charge = $pdo->prepare($chargeRequest);
$charge->execute(["uid" => $_SESSION['userid']]);
$charges = $charge->fetchAll(PDO::FETCH_ASSOC);
$noOfCards = 0;
$cr_cards = [];
$chgs  = [];
if ($charges) {
    // Determine the number of cards to be able to sort according to card name
    foreach ($charges as $entry) {
        if (!in_array($entry['cdname'], $cr_cards)) {
            array_push($cr_cards, $entry['cdname']);
        }
    }
    $noOfCards = count($cr_cards);
    // Now form arrays for each card
    for ($i=0; $i<$noOfCards; $i++) {
        $card_data = [];
        foreach ($charges as $item) {
            if ($item['cdname'] === $cr_cards[$i]) {
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
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reverse Charge</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Reverse one or more credit card charges" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="../styles/bootstrap.min.css" type="text/css" rel="stylesheet" />
    <link href="../styles/reverseCharge.css" type="text/css" rel="stylesheet" />
</head>

<body>
<?php require_once "../main/navbar.php"; ?>
<div id="page">
    <h3>Select charge(s) to reverse by checking the box adjacent to the charge</h3>
    <h4>This will have the effect of removing the charge from the card's accumulated
    expense, and having the charge placed back into the account from which it was 
    originally drawn</h4>
<?php if ($chgs_paid) : ?>
    <h4 id="paid">Charge(s) Successfully Reversed</h4>
<?php endif; ?>
    <div id="charge_data">
        <p id="cdcnt" style="display:none;"><?=$noOfCards;?></p>
        <div>
            <button id="reverse" class="btn btn-secondary" type="button">
            Reverse Charges</button>&nbsp;&nbsp;<span id="action">All checked
                boxes will have their respective charges reversed</span>
        </div><br />
    <?php if ($noOfCards === 0) : ?>
        <h3>You have no outstanding credit card charges</h3>
    <?php else : ?>
        <div id="main">
        <?php for ($j=0; $j<$noOfCards; $j++) : ?>
            <h4>For Credit Card
                <span class="cardtxt"><?=$cr_cards[$j];?>:</span></h4>
            <h5>Click on header to sort; again to reverse</h5>
            <div class="carddiv">
                <table class="sortable">
                    <thead>
                        <tr>
                            <th>Undo</th>
                            <th data-sort="amt">Amt<br />Chgd</th>
                            <th data-sort="std">Date<br />Entered</th>
                            <th data-sort="std">Account Charged</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php for ($k=0; $k<count($chgs[$j]); $k++) : ?>
                        <tr>
                            <td class="calign"><input type="checkbox" name="revchg[]"
                                value="<?=$chgs[$j][$k][0];?>" /></td>
                            <td class="ralign"><?=$chgs[$j][$k][1];?></td>
                            <td class="ralign"><?=$chgs[$j][$k][3];?></td>
                            <td><?=$chgs[$j][$k][2];?></td>
                        </tr>
                    <?php endfor; ?>
                    </tbody>
                </table><br />
            </div>
        <?php endfor; ?>
        </div>
    <?php endif; ?>
    </div>
    <?php require_once "../main/bootstrapModals.html"; ?>
</div>
<br />

<?php require_once "../main/bootstrapModals.html"; ?>

<script src="https://unpkg.com/@popperjs/core@2.4/dist/umd/popper.min.js"></script>
<script src="../scripts/bootstrap.min.js"></script>
<script src="../scripts/jquery-1.12.1.js"></script>
<script src="../scripts/menus.js"></script>
<script src="../scripts/reverseCharge.js" type="text/javascript"></script>
<script src="../scripts/tableSort.js"></script>
</body>

</html>