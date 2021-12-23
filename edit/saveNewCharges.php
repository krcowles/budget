<?php
/**
 * This script saves all #expense entries into the 'Charges' table entered on
 * the newBudgetPanels.php form. It collects any changes made to existing
 * data, and inserts new data. It then returns to the newBudget.php page,
 * or, if "Save and Return Later" was clicked, it redirects to the exit page.
 * PHP Version 7.3
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require "../database/global_boot.php";

$exit_and_return  = filter_input(INPUT_POST, 'exit3') === 'yes' ? true : false;

// get all pre-entered data, if any
if (isset($_POST['aeeamt'])) {
    $exp_dates = $_POST['aeedate'];
    $exp_cards = $_POST['oldcds'];
    $exp_amt   = $_POST['aeeamt'];
    $exp_payee = $_POST['aeepay'];
    $exp_ids   = $_POST['expids'];
    $exp_acct  = $_POST['oldchg'];
    $deletions = isset($_POST['edel']) ? $_POST['edel'] : false;
} else {
    $exp_amt = false;
}
if ($exp_amt) {
    // delete/update as needed
    if ($deletions) {
        for ($k=0; $k<count($deletions); $k++) {
            $del = "DELETE FROM `Charges` WHERE `expid` = {$deletions[$k]};";
            $pdo->query($del);
        }
    }
    // update Budgets table
    for ($m=0; $m<count($exp_amt); $m++) {
        if (!$deletions || $deletions && (!in_array($exp_ids[$m], $deletions))) {
            $updte = "UPDATE `Charges` SET `method`='Credit',`cdname`=?," .
                "`expdate`=?,`expamt`=?,`payee`=?,`acctchgd`=?,`paid` = 'N' " .
                "WHERE `expid`=?;";
            $req = $pdo->prepare($updte);
            $req->execute(
                [$exp_cards[$m], $exp_dates[$m], $exp_amt[$m], $exp_payee[$m],
                $exp_acct[$m], $exp_ids[$m]]
            );
        }
    }
}

// retrieve new data from form
$new_dates   = $_POST['edate'];
$new_cards   = $_POST['newcds'];
$new_amounts = $_POST['eamt'];
$new_payees  = $_POST['epay'];
$new_accts   = $_POST['chgto'];
// save the new stuff to the 'Budgets' tablex
for ($n=0; $n<count($new_amounts); $n++) {
    if (!empty($new_amounts[$n])) {
        $new = "INSERT INTO `Charges` (`userid`,`method`,`cdname`,`expdate`," .
            "`expamt`,`payee`,`acctchgd`,`paid`) VALUES (?,'Credit',?,?,?,?,?,'N');";
        $newchgs = $pdo->prepare($new);
        $newchgs->execute(
            [$_SESSION['userid'], $new_cards[$n], $new_dates[$n], 
            $new_amounts[$n], $new_payees[$n], $new_accts[$n]]
        );
    }
}

// retrieve the current saved 'setup' value:
$getSetupReq = "SELECT `setup` FROM `Users` WHERE `uid`=?;";
$getSetup = $pdo->prepare($getSetupReq);
$getSetup->execute([$_SESSION['userid']]);
$setupRow = $getSetup->fetch(PDO::FETCH_NUM);
$setup = $setupRow[0];
if (count($new_amounts) !== 0) {
    $setup[2] = '1';
}
$_SESSION['start'] = $setup;
// update the 'setup' value
$status = "UPDATE `Users` SET `setup` = :setup WHERE `uid` = :uid;";
$newstat = $pdo->prepare($status);
$newstat->execute(["setup" => $setup, "uid" => $_SESSION['userid']]);

if (!$exit_and_return) {
    $goto = "newBudgetPanels.php?pnl={$setup}";
} else {
    $goto = "../utilities/exitPage.php";
}
header("Location: {$goto}");
