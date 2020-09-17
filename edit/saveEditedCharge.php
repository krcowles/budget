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
        $carddates = $_POST[$dateset];
        $cardamts  = $_POST[$amtset];
        $cardchgs  = $_POST[$chgdset];
        $cardpays  = $_POST[$payset];
        $indx = 0;
        for ($n=0; $n<count($expmethod); $n++) {
            if ($expmethod[$n] === 'Credit' && $expcdname[$n] === $cr[$k]) {
                $tblid = $expid[$n];
                $newdate = trim(filter_var($carddates[$indx]));
                $newamt  = trim(filter_var($cardamts[$indx]));
                $newchg  = trim(filter_var($cardchgs[$indx]));
                $newpay  = trim(filter_var($cardpays[$indx]));
                $indx++;
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
