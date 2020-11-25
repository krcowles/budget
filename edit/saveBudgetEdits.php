<?php
/**
 * This module can use this script to make edits to the budget data.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require "../utilities/getAccountData.php";

$editedBuds  = $_POST['edbud'];
$editedCurrs  = $_POST['edcurr'];
for ($j=0; $j<count($account_names); $j++) {
    $tblid = $acctid[$j];
    $edreq = "UPDATE `Budgets` SET `budamt` = :amt," .
        "`current` = :curr WHERE `id` = :uid;";
    $edit = $pdo->prepare($edreq);
    $edit->execute(
        ["amt" => $editedBuds[$j], "curr" => $editedCurrs[$j], "uid" => $tblid]
    );
}

$backpg = "budgetEditor.php";
header("Location: {$backpg}");
