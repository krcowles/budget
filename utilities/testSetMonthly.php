<?php
/**
 * This module sets up the test of distributing monthly income
 * by setting funding and balances to 0 for all accounts.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require_once "../database/global_boot.php";
require "../utilities/getAccountData.php";

foreach ($acctid as $id) {
    $updateReq = "UPDATE `Budgets` SET `funded`=0, `current`=0 WHERE `id`={$id};";
    $pdo->query($updateReq);
}
echo "CLEAR";
