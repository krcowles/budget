<?php
/**
 * Either set or delete the contents of the 'definc' field in the Users table.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require "../database/global_boot.php";

// set current balance
$defincome = filter_input(INPUT_POST, 'amt');
$account   = "Deferred Income";
$updateDeferredReq 
    = "UPDATE `Budgets` SET `current` = ? WHERE `budname` = ? AND `userid` = ?;";
$update = $pdo->prepare($updateDeferredReq);
$update->execute([$defincome, $account, $_SESSION['userid']]);

// set the marker in Users
$defmonth = filter_input(INPUT_POST, 'til');
$setDefincReq = "UPDATE `Users` SET `definc` = ? WHERE `uid` = ?;";
$setDefinc = $pdo->prepare($setDefincReq);
$setDefinc->execute([$defmonth, $_SESSION['userid']]);

echo "OK";
