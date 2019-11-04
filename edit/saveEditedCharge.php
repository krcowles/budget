<?php
/**
 * This module is invoked from the modal window on enterCardData.php when a 
 * single, existing charge is called up for edits. It returns status 'OK' if
 * all is well.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../utilities/getCrData.php";
$status = 'NOT_FINISHED';
$changed_cardno = filter_input(INPUT_GET, 'cno', FILTER_SANITIZE_NUMBER_INT);
$changed_item   = filter_input(INPUT_GET, 'item', FILTER_SANITIZE_NUMBER_INT);

$indx = array('card1', 'card2', 'card3', 'card4');
switch($changed_cardno) {
case 0:
    $card = $indx[0];
    break;
case 1:
    $card = $indx[1];
    break;
case 2:
    $card = $indx[2];
    break;
case 3:
    $card = $indx[3];
}
$charges = $credit_charges[$card];
$amount = filter_input(INPUT_GET, 'amount');
$charges[$changed_item][0] = filter_input(INPUT_GET, 'chg');
$charges[$changed_item][1] = filter_input(INPUT_GET, 'date');
$charges[$changed_item][2] = filter_input(INPUT_GET, 'payee');
$charges[$changed_item][3] = filter_input(INPUT_GET, 'amount');
$credit_charges[$card] = $charges;

$handle = fopen($credit_data, "w");
fputcsv($handle, $headers);
for ($i=0; $i<$card_cnt; $i++) {
    if (count($credit_charges[$indx[$i]]) > 0) {
        foreach ($credit_charges[$indx[$i]] as $line) {
            fputcsv($handle, $line);
        }
    }
    if ($i < $card_cnt-1) {
        fputcsv($handle, array("next"));
    }  
}
fclose($handle);
$status = "OK";
echo $status;
