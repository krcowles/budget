<?php
/**
 * Save the user data entered on the 'Non-Monthlies' Expense page
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

// Datbase updates to existing data
$return_to = filter_input(INPUT_POST, 'return_type');
$old_recno = isset($_POST['orec']) ? $_POST['orec'] : false;
if ($old_recno) {
    $old_items = $_POST['item'];
    $old_freqs = $_POST['ofreq'];
    $old_amts  = $_POST['oamt'];
    $old_first = $_POST['ofirst'];
    $old_SA_yr = $_POST['oyrs'];
    $old_auto  = $_POST['oap'];
    $old_apday = $_POST['oapday'];
    $old_dels  = isset($_POST['rms']) ? $_POST['rms'] : false;
    $old_count = $old_items ? count($old_items) : 0;
    foreach ($old_apday as &$day) {
        $day = $day == '' ? 0 : (int) $day;
    }
    for ($j=0; $j<$old_count; $j++) {
        if (!$old_dels || !in_array($old_recno[$j], $old_dels)) {
            $saveItem = 'UPDATE `Irreg` SET `item`=?,`freq`=?,`amt`=?,' .
                '`first`=?,`SA_yr`=?,`APType`=?,`APDay`=? WHERE `record`=?;';
            $update = $pdo->prepare($saveItem);
            $update->execute(
                [$old_items[$j], $old_freqs[$j], $old_amts[$j], $old_first[$j], 
                $old_SA_yr[$j], $old_auto[$j], $old_apday[$j], $old_recno[$j]]
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
} else { // need to add the 'Non-Monthlies' account to user's budget
    $addbud = true;
}
// there is always at least one new row, whether data is present or not
$new_items  = $_POST['nitem'];
$new_freqs  = $_POST['nfreq'];
$new_amts   = $_POST['namt'];
$new_first  = $_POST['nfirst'];
$new_altyrs = $_POST['eyrs'];
$new_auto   = $_POST['nap'];
$new_apday  = $_POST['napday'];
foreach ($new_apday as &$day) {
    $day = $day == '' ? 0 : (int) $day;
}
 
for ($k=0; $k<count($new_items); $k++) {
    // save only rows where item is specified (complete row checked before submit)
    if (!empty($new_items[$k])) {
        $saveNew = "INSERT INTO `Irreg` (`userid`,`item`,`freq`,`amt`,`funds`," .
            "`expected`,`first`,`SA_yr`,`APType`,`APDay`,`mo_pd`) " .
            "VALUES (?, ?, ?, ?, '','',?, ?, ?, ?,'');";
        $newData = $pdo->prepare($saveNew);
        $newData->execute(
            [$user, $new_items[$k], $new_freqs[$k], $new_amts[$k],
            $new_first[$k], $new_altyrs[$k], $new_auto[$k], $new_apday[$k]]
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
