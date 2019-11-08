<?php
/**
 * This module will write new charges out, deleting those which have been
 * reconciled by the user.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "getCrData.php";

$selected = filter_input(INPUT_POST, 'card_sel');
$deletions = isset($_POST['del']) ? $_POST['del'] : false;

for ($j=0; $j<$card_cnt; $j++) {
    if ($selected === $cards[$j]) {
        $cardid = $j;
        break;
    }
}
$delid = 'card' . $cardid;
// index for $credit_charges:
switch ($cardid) {
case 0: 
    $chargeid = 'card1';
    break;
case 1:
    $chargeid = 'card2';
    break;
case 2:
    $chargeid = 'card3';
    break;
case 3:
    $chargeid = 'card4';
}
$updated = [];
for ($q=0; $q<count($credit_charges[$chargeid]); $q++) {
    $did = $delid . 'rec' . $q;
    if (!in_array($did, $deletions)) {
        array_push($updated, $credit_charges[$chargeid][$q]);
    }
}
$credit_charges[$chargeid] = $updated;
// write out updated charges
$handle = fopen($credit_data, "w");
fputcsv($handle, $crHeaders);
foreach ($credit_charges as $cardset) {
    foreach ($cardset as $entry) {
        fputcsv($handle, $entry);
    }
    fputcsv($handle, array("next"));
}
fclose($handle);

header("Location: reconcile.php");
