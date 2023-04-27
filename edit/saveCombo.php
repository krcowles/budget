<?php
/**
 * Save the user data entered on the Non-Monthly Expense page
 * PHP Version 7.4
 *
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
$user = $_SESSION['userid'];

require "../database/global_boot.php";

$addbud = false;
$budget = 0;
$relations = array( // no of payments in one year
    "Bi-Annually"   => 0.5,
    "Annually"      => 1,
    "Semi-Annually" => 2,
    "Quarterly"     => 4,
    "Bi-Monthly"    => 6
);

$return_to = filter_input(INPUT_POST, 'return_type');
$old_recno = isset($_POST['orec'])   ? $_POST['orec']   : false;
$old_items = isset($_POST['oitem'])  ? $_POST['oitem']  : false;
$old_freqs = isset($_POST['ofreq'])  ? $_POST['ofreq']  : false;
$old_amts  = isset($_POST['oamt'])   ? $_POST['oamt']   : false;
$old_first = isset($_POST['ofirst']) ? $_POST['ofirst'] : false;
$old_SA_yr = isset($_POST['osa_yr']) ? $_POST['osa_yr'] : false;
$old_dels  = isset($_POST['rms'])    ? $_POST['rms']    : false;
$old_count = $old_items ? count($old_items) : 0;
if ($old_count > 0) {
    for ($j=0; $j<$old_count; $j++) {
        if (!$old_dels || !in_array($old_recno[$j], $old_dels)) {
            $saveItem = 'UPDATE `Irreg` SET `item`=?, `freq`=?, `amt`=?,' .
                '`first`=?,`SA_yr`=? WHERE `record`=?;';
            $update = $pdo->prepare($saveItem);
            $update->execute(
                [$old_items[$j], $old_freqs[$j], $old_amts[$j], $old_first[$j], 
                $old_SA_yr[$j], $old_recno[$j]]
            );
            $payments = $relations[$old_freqs[$j]];
            $ann_bud  = $payments * $old_amts[$j];
            $budget  += $ann_bud/12;
        } elseif ($old_dels && in_array($old_recno[$j], $old_dels)) {
            $deleteReq = "DELETE FROM `Irreg` WHERE `record`=?";
            $deletion = $pdo->prepare($deleteReq);
            $deletion->execute([$old_recno[$j]]);
        }
    }
} else { // need to add this account to user's budget
    $addbud = true;
}
// there is always at least one new row, whether data is present or not
$new_items  = $_POST['item'];
$new_freqs  = $_POST['freq'];
$new_amts   = $_POST['amt'];
$new_first  = $_POST['first'];
$new_altyrs = $_POST['alts'];
$new_entries = count($new_items) - 1;
if ($new_entries > 0) {  // save only if new_entries > 1
    for ($k=0; $k<$new_entries; $k++) {
        $saveNew = "INSERT INTO `Irreg` (`userid`,`item`,`freq`,`amt`,`first`," .
            "`SA_yr`) VALUES (?, ?, ?, ?, ?, ?);";
        $newData = $pdo->prepare($saveNew);
        $newData->execute(
            [$user, $new_items[$k], $new_freqs[$k], $new_amts[$k],
            $new_first[$k], $new_altyrs[$k]]
        );
        $payments = $relations[$new_freqs[$k]];
        $ann_bud  = $payments * $new_amts[$k];
        $budget  += $ann_bud/12;
    }  
}
if ($addbud) { // a Non-Monthlies account does not yet exist
    // get current highest budpos
    $budposReq = "SELECT `budpos` FROM `Budgets` WHERE `userid` = :uid AND " .
    "`status` = 'A' ORDER BY 1 DESC LIMIT 1;";
    $lastpos = $pdo->prepare($budposReq);
    $lastpos->execute(["uid" => $user]);
    $budnos  = $lastpos->fetch(PDO::FETCH_ASSOC);
    $newno   = $budnos['budpos'] + 1;
    $budget = ceil($budget);
    $newsql = "INSERT INTO `Budgets` (`userid`,`budname`,`budpos`," .
        "`status`,`budamt`,`prev0`,`prev1`,`current`,`autopay`,`moday`," .
        "`autopd`,`funded`) VALUES (:uid,'Non-Monthlies',:pos,'A',:amt,'0','0'," .
        "'0','','0','','0');";
    $addinfo = $pdo->prepare($newsql);
    $pass = $addinfo->execute(
        [
            ":uid"  => $user,
            ":pos"  => $newno,
            ":amt"  => $budget
        ]
    ); 
} else {
    // update the budget amount
    $budUpdteReq = "UPDATE `Budgets` SET `budamt`=? WHERE `userid`=? AND " .
        "`budname`=?;";
    $budUpdte = $pdo->prepare($budUpdteReq);
    $budUpdte->execute([$budget, $user, 'Non-Monthlies']);
}
if ($return_to === 'self') {
    header("Location: combo.php");
} else {
    header("Location: ../main/displayBudget.php");
}
