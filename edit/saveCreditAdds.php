<?php
/**
 * This script is available to the user to add outstanding (unpaid) Credit
 * card charges that were skipped or forgotten during new budget creation.
 * PHP Version 7.1
 *
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to Date
 */
$user = filter_input(INPUT_POST, 'user');
require "../utilities/getCards.php";

$dates  = $_POST['ndate'];
$amts   = $_POST['namt'];
$payees = $_POST['npay'];
$accts  = $_POST['acct'];
// there are four entries for each card
$cardname = $cr[0];
$noOfCards = count($cr);
$noOfEntries = 4 * $noOfCards;
$cdindx = 1;
for ($i=0; $i<$noOfEntries; $i++) {
    if (!empty($amts[$i])) {
        $savecd = "INSERT INTO `Charges` (`user`,`method`,`cdname`,`expdate`," .
            "`expamt`,`payee`,`acctchgd`,`paid`) VALUES (:usr,'Credit',:nme," .
            ":dte,:amt,:pay,:acct,'N');";
        $entry = $pdo->prepare($savecd);
        $entry->execute(
            ["usr" => $user, "nme" => $cardname, "dte" => $dates[$i],
            "amt" => $amts[$i], "pay" => $payees[$i], "acct" => $accts[$i]]
        );
    }
    if ($i > 0 && $i%4 === 0) {
        $cdindx++;
        if ($cdindx < $noOfCards) {
            $cardname = $cr[$cdindx];
        }
    }
}
$back = "addCreditCharges.php?user=" . $user;
header("Location: {$back}");