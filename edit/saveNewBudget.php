<?php
/**
 * This script saves all budget entries into the 'Budgets' table. It collects any
 * changes made to existing data, and also inserts new data. It then returns to the
 * newBudgetPanels.php page, or if the 'Save and Return Later' button was clicked by
 * the user [i.e. 'exit1' is set], it exits to the exit page. Since the 'setup' field
 * already has the first bit set due to forming tmp accounts (or adding data), there
 * is no need to update it.
 * PHP Version 7.3
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require "../database/global_boot.php";

$pos   = filter_input(INPUT_POST, 'lastpos', FILTER_VALIDATE_INT);
$exit  = filter_input(INPUT_POST, 'exit1') === 'no' ? false : true;

// get all pre-entered data, if any
if (isset($_POST['svdname'])) {
    $chg_accounts = $_POST['svdname'];
    $chg_budgets  = $_POST['svdbud'];
    $chg_balances = $_POST['svdbal'];
    $deletions    = isset($_POST['remove']) ? $_POST['remove'] : false;
    $chg_ids      = $_POST['ids'];
} else {
    $chg_accounts = false;
}
if ($chg_accounts) {
    // delete/update as needed
    if ($deletions) {
        for ($k=0; $k<count($deletions); $k++) {
            $del = "DELETE FROM `Budgets` WHERE id = {$deletions[$k]};";
            $pdo->query($del);
        }
    }
    // update Budgets table
    for ($m=0; $m<count($chg_accounts); $m++) {
        if (!$deletions || $deletions && (!in_array($chg_ids[$m], $deletions))) {
            $updte = "UPDATE `Budgets` SET `budname` = ?, `budamt` = ?,
                `current` = ? WHERE `id` = ?;";
            $req = $pdo->prepare($updte);
            $req->execute(
                [$chg_accounts[$m], $chg_budgets[$m], 
                $chg_balances[$m], $chg_ids[$m]]
            );
        }
    }
}

// retrieve new data from form
$new_accounts = $_POST['acctname'];
$new_budgets  = $_POST['bud'];
$new_balances = $_POST['bal'];
$next = $pos + 1;
// save the new stuff to the 'Budgets' table
for ($n=0; $n<count($new_accounts); $n++) {
    if (!empty($new_accounts[$n])) {
        $newReq = "INSERT INTO `Budgets` (`userid`,`budname`,`budpos`,`status`," .
            "`budamt`,`prev0`,`prev1`,`current`,`autopay`,`moday`,`autopd`," .
            "`funded`) VALUES (?,?,?,'A',?,'0','0',?,'','0','','0');";
        $new = $pdo->prepare($newReq);
        $new->execute(
            [$_SESSION['userid'], $new_accounts[$n], $next,
            $new_budgets[$n], $new_balances[$n]]
        );
        $next++;
    }
}

// retrieve current value of 'setup'
$getSetupReq = "SELECT `setup` FROM `Users` WHERE `uid`=?;";
$getSetup = $pdo->prepare($getSetupReq);
$getSetup->execute([$_SESSION['userid']]);
$setupRow = $getSetup->fetch(PDO::FETCH_ASSOC);
$setup = $setupRow['setup'];

if (!$exit) { // 'normal' form submit
    $next = "newBudgetPanels.php?pnl={$setup}";
} else { // 'leave and return' button
    $next = "../utilities/exitPage.php";
}
header("Location: {$next}");
