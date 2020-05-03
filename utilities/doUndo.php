<?php
/**
 * This module is called from a form submit on undoExpense.php.
 * It will operate on any data passed from that page to remove an expense
 * item applying the removed charge back to the account from which it was drawn.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require_once "../database/global_boot.php";

$user = filter_input(INPUT_POST, 'user');

$undos = $_POST['revexp'];
if (isset($undos)) {
    foreach ($undos as $reverse) {
        // the checkbox value corresponds to the name id for exp & acc
        $expid = 'amt' . $reverse;
        $accid = 'acc' . $reverse; 
        $exp  = filter_input(INPUT_POST, $expid);
        $acct = filter_input(INPUT_POST, $accid);
        $revRequest = "UPDATE `Charges` SET `expamt`='0',`paid` ='Y' " .
            "WHERE `expid` = :expid;";
        $rev = $pdo->prepare($revRequest);
        $rev->execute(["expid" => $reverse]);
        $budRequest = "UPDATE `Budgets` SET `current`=`current` + {$exp} WHERE " .
            "`user` = :user AND `budname` = :acct;";
        $budupdate = $pdo->prepare($budRequest);
        $budupdate->execute(["user" => $user, "acct" => $acct]);
    }
}
$return = "undoExpense.php?user=" . $user . "&paid=yes";
header("Location: {$return}");
