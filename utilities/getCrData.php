<?php
/**
 * This utility verifies then reads the $credit_data file. The credit charge data
 * is entered into arrays (see bottom commented listing), as are the credit card
 * names and debit card names.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require_once "../utilities/timeSetup.php";
require_once "budgetFunctions.php";

$crStatus = "OK";
if (file_exists($credit_data)) {
    $cd_file = fopen($credit_data, "r");
    $crHeaders = fgetcsv($cd_file);
    $crHeaders = cleanupExcel($crHeaders);
    if ($crHeaders[0] === 'None') {
        $crStatus = "E6: No credit data entered yet";
    }
} else {
    $crStatus = "E7: File does not exist";
}
if ($crStatus === "OK") {
    // allowing for up to 4 cards at present
    $credit_charges = array('card1' => array(), 'card2' => array(), 
        'card3' => array(), 'card4' => array());
    $cards = [];
    $debits = [];
    $debno = 0;
    $cardno = 0;
    for ($a=0; $a<count($crHeaders); $a+=2) {
        if ($crHeaders[$a+1] === 'Credit') {
            $cards[$cardno] = $crHeaders[$a];
            $cardno++;
        } elseif ($crHeaders[$a+1] === 'Debit') {
            $debits[$debno] = $crHeaders[$a];
            $debno++;
        }
    }
    $card_cnt = count($cards);
    $cardno = 0;
    // NOTE: the array size (# elements) is set by the $headers
    while (($charge = fgetcsv($cd_file)) !== false) {
        if ($charge[0] !== 'next') {
            $data = floatval($charge[3]);
            $charge[3] = number_format($data, 2, '.', ',');
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
 * This module produces the following data:
 *  $crStatus   file status for credit data
 *  $crHeaders  card name and type info on line 1 of CSV file
 *  $cards      names of all current credit cards
 *  $card_cnt   no of credit cards specified in $headers
 *  $credit_charges:
 *              All card charges in an indexed array
 *  $debits     An array holding any debit card names
 */ 
