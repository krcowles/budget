<?php
/**
 * This file allows the user to add charges to any credit cards currently in place
 * PHP Version 7.1
 * 
 * @package BUDGET
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require_once "timeSetup.php";
require_once "budgetFunctions.php";

$handle = fopen($credit_data, "r");
$headers = fgetcsv($handle);
$headers = cleanupExcel($headers);
$cardno = 0;
for ($a=0; $a<count($headers); $a+=2) {
    if ($headers[$a+1] === 'Credit') {
        $cards[$cardno] = $headers[$a];
        $cardno++;
    }
}
$selectHtml = PHP_EOL . '<select id="sel" name="card_sel">' . PHP_EOL;
for ($k=0; $k<count($cards); $k++) {
    if ($k === 0) {
        $selectHtml .= '<option selected="selected" value="' . $cards[$k] .
            '">' . $cards[$k] . '</option>' . PHP_EOL;
    } else {
        $selectHtml .= '<option value="' . $cards[$k] . '">' . 
            $cards[$k] . '</option>' . PHP_EOL;
    }
}
$selectHtml .= '</select>' . PHP_EOL;
/**
 * This module produces the html for a select box to be used on various sheets
 */
