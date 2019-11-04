<?php
/**
 * This module will save (only) new entries made by a user against one specific
 * credit card.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../utilities/getCrData.php";

$charge_card = filter_input(INPUT_POST, 'card_sel');
$newcharges = $_POST['newcharge'];
$newdates   = $_POST['newdate'];
$newpayees  = $_POST['newpayee'];
$newamts    = $_POST['newamt'];
// id the card against which charges are being applied
$indices = array('card1', 'card2', 'card3', 'card4');
$indx = 0;
for ($k=0; $k<$card_cnt; $k++) {
    if ($charge_card === $cards[$k]) {
        $indx = $k;
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
            filter_var($newpayees[$j]),
            filter_var($newamts[$j])
        );
        array_push($credit_charges[$card_indx], $newitem);
        $newitem = null;
    }
}
$handle = fopen($credit_data, "w");
fputcsv($handle, $headers);
for ($i=0; $i<$card_cnt; $i++) {
    if (count($credit_charges[$indices[$i]]) > 0) {
        foreach ($credit_charges[$indices[$i]] as $line) {
            fputcsv($handle, $line);
        }
    }
    if ($i < $card_cnt-1) {
        fputcsv($handle, array("next"));
    }  
}
fclose($handle);
header("Location: enterCardData.php");