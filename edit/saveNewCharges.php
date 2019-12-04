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
require "../database/global_boot.php";

$user = filter_input(INPUT_POST, 'user');

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
        $new = "INSERT INTO `Charges` (`user`,`method`,`cdname`,`expdate`," .
            "`expamt`,`payee`,`paid`) VALUES ('" . $user . "','Credit','" . 
            $new_cards[$n] . "','" . $new_dates[$n] . "','" . $new_amounts[$n] . 
            "','" . $new_payees[$n] . "','N');";
        try {
            $pdo->query($new);
        } catch (PDOException $e) {
            echo "Got " . $e->getMessage();
        }
    }
}

$goto = "newBudgetPanels.php?pnl=three&user=" . $user;
header("Location: {$goto}");