<?php
/**
 * This allows the user to modify Expense data in the `Charges` table
 * for expenses already paid in the last 30 days.
 * PHP Version 7.4
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require_once "../database/global_boot.php";

$meths = $_POST['meths'];  // "Check" or "Debit"
$dbcrs = $_POST['drcrds']; // Debit Card Name
$date  = $_POST['date'];   // Updated date expense paid
$amt   = $_POST['amt'];    // Updated amount expensed
$paye  = $_POST['pay'];    // Updated payee
$chgd  = $_POST['chgd'];   // Updated account charged
$id    = $_POST['exid'];   // `Charges` table id
$org   = $_POST['org'];    // Original amount pd 
                           // (in case a change of account charged)
$oact  = $_POST['oact'];   // Original account charged

/**
 * ----- Possible change impacts, listed by priority: -----
 * 1. '$chgd' (charged acct) changed: In this case, the original account ($oact)
 *     must be refunded its -original- expense amount($org), and the new acct ($chgd)
 *     is charged the $amt -now posted-. All other fields in `Charges` are also
 *     updated with posted data.
 * 2. '$amt' changed: if the amount charged changed AND the account changed, it has
 *    already been addressed in item1. Otherwise, update the current balance for the
 *    acct based on the delta of the change.
 * 3. Changing date affects nothing [except whether or not it gets included in the
 *    'past 30 days' test];
 * 4. '$paye' changed: no impact to anything outside the db field 'payee'
 * 5. Whether 'Debit' or 'Check' ($meths) only changes historical reporting and 
 *    where the charge appears in the 'Review Expenses' tab
 * 6. Changing $dbcrs only changes the db field `cardname`
 */

// Address above items via the following 'if' for every entry in the loop:
for ($j=0; $j<count($chgd); $j++) {
    if ($chgd[$j] !== $oact[$j]) {
        // Item 1: Look for any cases where the 'account charged' is being changed
        // Refund the previously charged account with its previously expensed amt:
        $refundReq 
            = "UPDATE `Budgets` SET `current`=(@cur_value := `current`) + ? WHERE " .
                "`userid`=? AND `budname`=? AND (`status`='A' OR `status`='T');";
        $refund = $pdo->prepare($refundReq);
        $refund->execute([$org[$j], $_SESSION['userid'], $oact[$j]]);
        // Now update the newly charged account
        $newExpReq 
            = "UPDATE `Budgets` SET `current`=(@cur_value := `current`) - ? WHERE " .
                "`userid`=? AND `budname`=? AND (`status`='A' OR `status`='T');";
        $newExp = $pdo->prepare($newExpReq);
        $newExp->execute([$amt[$j], $_SESSION['userid'], $chgd[$j]]);
        // Update the 'Charges' table with new data
        if ($meths[$j] !== 'Check') {
            $method = 'Debit';
            $cardname = $dbcrs[$j];
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

    } else {
        // Item 2: 'account charged' stayed the same...
        // update any $ amount changes to the account:
        if ($amt[$j] !== $org[$j]) {
            $delta = $org[$j] - $amt[$j]; // negative if new amt is now greater
        } else {
            $delta = 0; // if $amt did NOT change 
        }
        if ($meths[$j] !== 'Check') {
            $method = 'Debit';
            $cardname = $dbcrs[$j];
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
