<?php
/**
 * When a credit or debit card is deleted, this script will update
 * the Budgets table by eliminating autopay data associated with the
 * deleted card.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../database/global_boot.php";
$records = $_POST['set'];

$budids  = json_decode($records);
foreach ($budids as $acct) {
    $updateAutoPayReq = "UPDATE `Budgets` SET `autopay`='', `moday`=0, " .
        "`autopd`='', `funded`=0 WHERE `id`={$acct};";
    $pdo->query($updateAutoPayReq);
}
