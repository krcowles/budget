<?php
/**
 * This allows the user to modify Expense data in the `Charges` table
 * for expenses already paid in the last 30 days.
 * PHP Version 7.1
 * 
 * @package BUDGET
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require_once "../database/global_boot.php";

$type = $_POST['types']; // Debit card name, or 'Check or Draft'
$date = $_POST['date'];  // Updated date expense paid
$amt  = $_POST['amt'];   // Updated amount expensed
$paye = $_POST['pay'];   // Updated payee
$chgd = $_POST['chgd'];  // Updated account charged
$id   = $_POST['exid'];  // `Charges` table id
$org  = $_POST['org'];   // Original amount pd (in case a change of account charged)
$oact = $_POST['acct'];  // Original account charged

/**
 * Possible change impacts, listed by priority:
 * 1. '$chgd' (charged acct) changed: In this case, the old acct must be refunded
 *    its original expense, and the new acct charged as now posted
 * 2. '$amt' changed (and not item 1):
 *    Update the current balance for the acct based on the delta change
 * 3. '$type' changed from 'Check' to 'Debit', or vice versa, or change in Dr Card
 *    Since these expenses aren't accumulated based on debit method,
 *    this change is non-impactful;
 * 4. '$date' changed: no impact
 * 5. '$paye' changed: no impact
 */

for ($j=0; $j<count($chgd); $j++) {
    // Look for any cases where the 'account charged' is being changed
    if ($chgd[$j] !== $oact[$j]) {
        // Refund the previously charged account with its previously expensed amt:
        $refundReq 
            = "UPDATE `Budgets` SET `current`=(@cur_value := `current`) + ? WHERE " .
                "`userid`=? AND `budname`=? AND (`status`='A' OR `status`='T');";
        $refund = $pdo->prepare($refundReq);
        $refund->execute([$org[$j], $_SESSION['userid'], $oact[$j]]);
        // Now update Budgets with the new data
        $newExpReq 
            = "UPDATE `Budgets` SET `current`=(@cur_value := `current`) - ? WHERE " .
                "`userid`=? AND `budname`=? AND (`status`='A' OR `status`='T');";
        $newExp = $pdo->prepare($newExpReq);
        $newExp->execute([$amt[$j], $_SESSION['userid'], $chgd[$j]]);
        // Update the 'Charges' table with new data
        if ($type[$j] !== 'Check or Draft') {
            $method = 'Debit';
            $cardname = $type[$j];
        } else {
            $method = 'Check';
            $cardname = 'Check or Draft';
        }
        $payoutReq = "UPDATE `Charges` SET " .
            "`method` = :meth,`cdname` = :cd,`expdate` = :dte,`expamt` = :amt," .
            "`payee` = :pay,`acctchgd` = :acct WHERE `expid` = :eid;";
        $payout = $pdo->prepare($payoutReq);
        $payout->execute(
            ["meth" => $method, "cd" => $cardname, "dte" => $date[$j], 
            "amt" => $amt[$j], "pay" => $paye[$j], "acct" => $chgd[$j], 
            "eid" => $id[$j]]
        );
    } else { // account charged stayed the same...
        // update any $ amount changes to the account:
        if ($amt[$j] !== $org[$j]) {
            $delta = $org[$j] - $amt[$j]; // negative if new amt is now greater
        } else {
            $delta = 0;
        }
        if ($type[$j] !== 'Check or Draft') {
            $method = 'Debit';
            $cardname = $type[$j];
        } else {
            $method = 'Check';
            $cardname = 'Check or Draft';
        }
        $updateBudReq
            = "UPDATE `Budgets` SET `current`=(@cur_value := `current`) + ? WHERE " .
                "`userid`=? AND `budname`=? AND (`status`='A' OR `status`='T');";
        $updateBud = $pdo->prepare($updateBudReq);
        $updateBud->execute([$delta, $_SESSION['userid'], $chgd[$j]]);
        // post the new `Charges` data
        $updateNewReq = "UPDATE `Charges` SET " .
            "`method` = :meth,`cdname` = :cd,`expdate` = :dte,`expamt` = :amt," .
            "`payee` = :pay,`acctchgd` = :acct WHERE `expid` = :eid;";
        $updateNew = $pdo->prepare($updateNewReq);
        $updateNew->execute(
            ["meth" => $method, "cd" => $cardname, "dte" => $date[$j], 
            "amt" => $amt[$j], "pay" => $paye[$j], "acct" => $chgd[$j], 
            "eid" => $id[$j]]
        );
    }
}

$back = "editExpenses.php";
header("Location: {$back}");
