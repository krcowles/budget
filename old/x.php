<?php
/**
 * This module will save (only) new entries made by a user against one specific
 * credit card.
 * PHP Version 7.1
 */
require "../utilities/getCrData.php";

$charge_card = filter_input(INPUT_POST, 'card_sel', FILTER_SANITIZE_NUMBER_INT);
$newcharges = $_POST['newcharge'];
$newdates   = $_POST['newdate'];
$newpayees  = $_POST['newpayee'];
$newamts    = $_POST['newamt'];
// id the card against which charges are being applied
$indices = array('card1', 'card2', 'card3', 'card4');
$indx = 0;
for ($i=0; $i<$card_cnt; $i++) {
    if ($charge_card === $cards[$i]) {
        $indx = $i;
        break;
    }
}
$card_indx = $indices[$indx];
// add new information
for ($j=0; $j<count($newcharges); $j++) {
    if (!empty($newcharges[$j])) {
        $newitem = array(
            filter_var($newcharges[$j]),
            filter_var($newdates[$j]),
            filter_var($newpayess[$j]),
            filter_var($newamts[$j])
        );
        array_push($credit_charges[$card_indx], $newitem);
        $newitem = null;
    }
}
echo "x";