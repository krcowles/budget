<?php
/**
 * This utility 'Undoes' the incoming deposit(s) by removing the dollar
 * amounts from the user's 'Undistributed Funds' account.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../database/global_boot.php";

$depositids = filter_input(INPUT_POST, 'array');
$ids = json_decode($depositids);

foreach ($ids as $id) {
    $getRecReq = "SELECT * FROM `Deposits` WHERE `depid` = ?;";
    $getRec = $pdo->prepare($getRecReq);
    $getRec->execute([$id]);
    $user_dep = $getRec->fetch(PDO::FETCH_ASSOC);
    $userid = $user_dep['userid'];
    $amt = (float) $user_dep['amount'];
    $acctUpdteReq = "UPDATE `Budgets` SET `current` = `current` - ? " .
        "WHERE `budname`='Undistributed Funds' AND `userid`=?;";
    $acctUpdte = $pdo->prepare($acctUpdteReq);
    $acctUpdte->execute([$amt, $userid]);
    // Now delete the deposit to eliminate it from reports
    $delDepReq = "DELETE FROM `Deposits` WHERE `depid` = ?;";
    $delDep = $pdo->prepare($delDepReq);
    $delDep->execute([$id]);
}
