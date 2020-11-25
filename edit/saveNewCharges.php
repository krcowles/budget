<?php
/**
 * This script saves all #expense entries into the 'Charges' table.
 * It collects any changes made to existing data, and inserts new data.
 * It then returns to the newBudget.php page.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require "../database/global_boot.php";

$exit_and_return  = filter_input(INPUT_POST, 'exit3') === 'yes' ? true : false;

$setup = $exit_and_return ? '001' : '111';
$_SESSION['start'] = $setup;
$status = "UPDATE `Users` SET `setup` = :setup WHERE `uid` = :uid;";
$newstat = $pdo->prepare($status);
$newstat->execute(["setup" => $setup, "uid" => $_SESSION['userid']]);

// get all pre-entered data, if any
if (isset($_POST['aeeamt'])) {
    $exp_dates    = $_POST['aeedate'];
    $exp_cards  = $_POST['oldcds'];
    $exp_amt      = $_POST['aeeamt'];
    $exp_payee    = $_POST['aeepay'];
    $exp_ids      = $_POST['expids'];
    $deletions    = isset($_POST['edel']) ? $_POST['edel'] : false;
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
            $updte = "UPDATE `Charges` SET `method` = 'Credit',`cdname` = ?, " .
                "`expdate` = ?,`expamt` = ?, `payee` = ?, `paid` = 'N' " .
                "WHERE `expid` = ?;";
            $req = $pdo->prepare($updte);
            $req->execute(
                [$exp_cards[$m], $exp_dates[$m], $exp_amt[$m], 
                $exp_payee[$m], $exp_ids[$m]]
            );
        }
    }
}

// retrieve new data from form
$new_dates = $_POST['edate'];
$new_cards  = $_POST['newcds'];
$new_amounts = $_POST['eamt'];
$new_payees = $_POST['epay'];
// save the new stuff to the 'Budgets' tablex
for ($n=0; $n<count($new_amounts); $n++) {
    if (!empty($new_amounts[$n])) {
        $new = "INSERT INTO `Charges` (`userid`,`method`,`cdname`,`expdate`," .
            "`expamt`,`payee`,`paid`) VALUES ('" . $_SESSION['userid'] .
            "','Credit','" . $new_cards[$n] . "','" . $new_dates[$n] . "','" .
            $new_amounts[$n] . "','" . $new_payees[$n] . "','N');";
        $pdo->query($new);
    }
}
if ($setup === '111') { // don't return
    $goto = "../main/displayBudget.php";
} else {
    $goto = "../utilities/exitPage.php";
}
header("Location: {$goto}");
