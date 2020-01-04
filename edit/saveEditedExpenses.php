<?php
/**
 * This allows the user to modify expense data in the `Charges` table
 * for expenses already paid in the last 30 days.
 * PHP Version 7.1
 * 
 * @package BUDGET
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
$user = filter_input(INPUT_POST, 'user');
require_once "../database/global_boot.php";
$id   = $_POST['exid'];
$type = $_POST['type'];
$name = $_POST['cdname'];
$date = $_POST['date'];
$amt  = $_POST['amt'];
$paye = $_POST['pay'];
$acct = $_POST['chgd'];
$org  = $_POST['org']; // original amount to detect a change
// update the 'Charges' table with these items
for ($j=0; $j<count($id); $j++) {
    $update = "UPDATE `Charges` SET " .
        "`method` = :type,`cdname` = :cd,`expdate` = :dte,`expamt` = :amt," .
        "`payee` = :pay,`acctchgd` = :acct WHERE `expid` = :eid;";
    $newdat = $pdo->prepare($update);
    $newdat->execute(
        ["type" => filter_var($type[$j]), "cd" => filter_var($name[$j]),
        "dte" => filter_var($date[$j]), "amt" => filter_var($amt[$j]),
        "pay" => filter_var($paye[$j]), "acct" => filter_var($acct[$j]),
        "eid" => filter_var($id[$j])]
    ); 
    if (filter_var($org[$j]) !== filter_var($amt[$j])) {
        // update account charged
        $old = floatval(filter_var($org[$j]));
        $new = floatval(filter_var($amt[$j]));
        $delta = $old - $new;
        $buditem = "SELECT `current`,`id` FROM `Budgets` WHERE `user` = :uid " .
            "AND `budname` = :bud;";
        $buddat = $pdo->prepare($buditem);
        $buddat->execute(["uid" => $user, "bud" => filter_var($acct[$j])]);
        $olddat = $buddat->fetch(PDO::FETCH_ASSOC);
        $adjusted = floatval($olddat['current']) + $delta;
        $newacct = "UPDATE `Budgets` SET `current` = :cur WHERE `id` = :bid;";
        $updte = $pdo->prepare($newacct);
        $updte->execute(["cur" => $adjusted, "bid" => $olddat['id']]);
    }
}
$back = "editExpenses.php?user=" . rawurlencode($user);
header("Location: {$back}");
