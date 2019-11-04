<?php
/**
 * This utility looks for the account's *_charges.csv file and uses it to 
 * populate the enterCardData.php page.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require_once "../utilities/timeSetup.php";

$nodata = false;
if (file_exists($credit_data)) {
    $cd_file = fopen($credit_data, "r");
    $headers = fgetcsv($cd_file);
    if ($headers[0] === 'None') {
        $nodata = true;
    }
} else {
    $nodata = true;
}
if ($nodata) {
    echo '<script type="text/javascript">alert("No credit/debit cards have been ' .
        'assigned: use "Account Management Tools" and select "Setup/Change ' .
        'Debit/Credit Info");window.open("../main/budget.php", "_self");</script>';
} else {
    // allowing for up to 4 cards at present
    $credit_charges = array('card1' => array(), 'card2' => array(), 
        'card3' => array(), 'card4' => array());
    $cards = [];
    $cardno = 0;
    for ($a=0; $a<count($headers); $a+=2) {
        if ($headers[$a+1] === 'Credit') {
            $cards[$cardno] = $headers[$a];
            $cardno++;
        }
    }
    $card_cnt = count($cards);
    $cardno = 0;
    // NOTE: the array size (# elements) is set by the $headers?
    while (($charge = fgetcsv($cd_file)) !== false) {
        if ($charge[0] !== 'next') {
            switch ($cardno) {
            case 0:
                array_push($credit_charges['card1'], $charge);
                break;
            case 1:
                array_push($credit_charges['card2'], $charge);
                break;
            case 2:
                array_push($credit_charges['card3'], $charge);
                break;
            case 3:
                array_push($credit_charges['card4'], $charge);
                break;
            }
        } else {
            $cardno++;
        }
    }
    fclose($cd_file);
}
/**
 * Return to caller with the following data:
 *  $headers    card name and type info on line 1 of CSV file
 *  $cards      names of all current credit cards
 *  $card_cnt   no of credit cards specified in $headers
 *  $credit_charges:
 *              All card charges in an indexed array
 */ 
