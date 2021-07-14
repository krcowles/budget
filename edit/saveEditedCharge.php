<?php
/**
 * This module saves the data presented to the user on editCreditCharges.php,
 * whether edited or not.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require "../utilities/getAccountData.php";
require "../utilities/getCards.php";
require "../utilities/getExpenses.php";

$card_items = $_POST['cnt'];
// get each card's data set and update it:
for ($k=0; $k<count($cr); $k++) {
    if ($card_items[$k] > 0) {
        $dateset = 'cr' . $k . 'date';
        $amtset  = 'cr' . $k . 'amt';
        $chgdset = 'cr' . $k . 'chgd';
        $payset  = 'cr' . $k . 'pay';
        $carddates = $_POST[$dateset];  // no impact change
        $cardamts  = $_POST[$amtset];   // adjust the (old) account charged
        $cardchgs  = $_POST[$chgdset];  // update old acct charged and charge new one
        $cardpays  = $_POST[$payset];   // no impact change
        $indx = 0;
        for ($n=0; $n<count($expmethod); $n++) {
            if ($expmethod[$n] === 'Credit' && $expcdname[$n] === $cr[$k]) {
                $tblid = $expid[$n];
                // capture current data to ensure correct updates for impacted fields
                $olddatReq
                    = "SELECT `expamt`,`acctchgd` FROM `Charges` WHERE `expid` = ?;";
                $olddat = $pdo->prepare($olddatReq);
                $olddat->execute([$tblid]);
                $current = $olddat->fetch(PDO::FETCH_ASSOC);
                // updated values:
                $newdate = trim(filter_var($carddates[$indx]));
                $newamt  = trim(filter_var($cardamts[$indx]));
                $newchg  = trim(filter_var($cardchgs[$indx]));
                $newpay  = trim(filter_var($cardpays[$indx]));
                if ($newamt !== $current['expamt']
                    && $newchg === $current['acctchgd'] // changed amt, not acct
                ) {
                    // the delta will be returned to the current charged acct
                    $delta = $current['expamt'] - $newamt;
                    $modBudReq 
                        = "UPDATE `Budgets` SET `current`=(@cur_value := `current`)"
                            . " + {$delta} WHERE `userid`=? AND `budname`=? AND "
                            . "(`status`='A' OR `status`='T')";
                    $modBud = $pdo->prepare($modBudReq);
                    $modBud->execute(
                        [$_SESSION['userid'], $current['acctchgd']]
                    );
                }
                if ($newchg !== $current['acctchgd']) { // the account has changed
                    if ($newamt !== $current['expamt']) { // amt has changed:     
                        // don't refund the new amt, refund the old amt
                        $delta = $current['expamt'];
                    } else { // amt hasn't changed: 
                        // refund the new amount
                        $delta = $newamt;
                    }  
                    $refundReq
                        = "UPDATE `Budgets` SET `current`=(@cur_value := `current`)"
                            . " + {$delta} WHERE `userid`=? AND `budname`=? AND "
                            . "(`status`='A' OR `status`='T')";
                    $refund = $pdo->prepare($refundReq);
                    $refund->execute(
                        [$_SESSION['userid'], $current['acctchgd']]
                    );
                    // in either case, charge the new amt to the new budge acct.
                    $chargeReq
                        = "UPDATE `Budgets` SET `current`=(@cur_value := `current`)"
                            . " - {$newamt} WHERE `userid`=? AND `budname`=? AND "
                            . "(`status`='A' OR `status`='T')";
                    $charge = $pdo->prepare($chargeReq);
                    $charge->execute([$_SESSION['userid'], $newchg]);
                }
                $indx++;
                // update the record
                $update = "UPDATE `Charges` SET `expdate` = :dte,`expamt` = :amt," .
                    "`payee` = :payee, `acctchgd` = :chgto WHERE `expid` = :tbl;";
                $updtchg = $pdo->prepare($update);
                $updtchg->execute(
                    ["dte" => $newdate, "amt" => $newamt, "payee" => $newpay,
                    "chgto" => $newchg, "tbl" => $tblid]
                );
            }
        }
    }
}
$back = "editCreditCharges.php";
header("Location: {$back}");
