<?php
/** 
 * This script saves any data entered by the user (or previously existing data,
 * changed or unchanged) to $credit_data.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../utilities/timeSetup.php";

$cards = $_POST['card'];
$types = $_POST['ctype'];
$csvline = [];
for ($i=0; $i<count($cards); $i++) {
    if (!empty($cards[$i])) {
        array_push($csvline, filter_var($cards[$i]));
        array_push($csvline, filter_var($types[$i]));
    }
}
if (empty($csvline[0])) {
    $csvline[0] = "None";
}
$chgcards = fopen($credit_data, "w");
//add BOM to fix UTF-8 in Excel 
fputs($chgcards, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
fputcsv($chgcards, $csvline);
fclose($chgcards);
header("Location: cardSetup.php");
