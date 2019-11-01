<?php
/** 
 * Save the Autopay data established by the user
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "getBudgetData.php";
$method = $_POST['amethod'];
$dom = $_POST['day'];
for ($i=0; $i<count($account_names); $i++) {
    if (!empty($dom[$i]) && intval($dom[$i]) > 0 && intval($dom[$i]) <= 31) {
        $autopay[$i] = filter_var($method[$i], FILTER_SANITIZE_STRING);
        $day[$i] = filter_var($dom[$i], FILTER_SANITIZE_NUMBER_INT);
    } else {
        $autopay[$i] = '';
        $day[$i] = '';
    }
}
$lines = [];
for ($j=0; $j<count($account_names); $j++) {
    $entry = array($account_names[$j], $budgets[$j], $prev0[$j], $prev1[$j],
        $current[$j], $autopay[$j], $day[$j]);
        array_push($lines, $entry);
}
$handle = fopen($budget_data, "w");
if ($handle !== false) {
    //add BOM to fix UTF-8 in Excel 
    fputs($handle, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
    fputcsv($handle, $headers);
    foreach ($lines as $line) {
        fputcsv($handle, $line);
    }
    fclose($handle);
} else {
    echo "No save";
}
header("Location: autopay.php");
