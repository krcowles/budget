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

$handle = fopen($credit_data, "r");
$headers = fgetcsv($handle);
$cardno = 0;
for ($a=0; $a<count($headers); $a+=2) {
    if ($headers[$a+1] === 'Credit') {
        $cards[$cardno] = $headers[$a];
        $cardno++;
    }
}
$selectHtml = '<select name="card_sel">' . PHP_EOL;
foreach ($cards as $opt) {
    $selectHtml .= '<option value="' . $opt . '">' . $opt . '</option>' . PHP_EOL;
}
$selectHtml .= '</select>' . PHP_EOL;
