<?php
/**
 * This module is called from a form submit on reverseCharge.php.
 * It will operate on any data passed back from that page to remove
 * an expense from a credit card and apply the removed charge back
 * to the account from which it was drawn.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require_once "../database/global_boot.php";

$undos = $_POST['revchg'];
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
            "`userid` = :uid AND `budname` = :acct;";
        $budupdate = $pdo->prepare($budRequest);
        $budupdate->execute(["uid" => $_SESSION['userid'], "acct" => $acct]);
    }
}
$return = "reverseCharge.php?paid=yes";
header("Location: {$return}");
