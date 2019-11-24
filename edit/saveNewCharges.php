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
    $exp_methods  = $_POST['esel'];
    $exp_amt      = $_POST['aeeamt'];
    $exp_payee    = $_POST['aeepay'];
    $deletions    = isset($_POST['edel']) ? $_POST['edel'] : false;
    $exp_ids      = $_POST['expids'];
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
            $updte = "UPDATE `Charges` SET `method` = ?, `expdate` = ?,
                `expamt` = ?, `payee` = ? WHERE `expid` = ?;";
            $req = $pdo->prepare($updte);
            try {
                $req->execute(
                    [$exp_methods[$m], $exp_dates[$m], 
                    $exp_amt[$m], $exp_payee[$m], $exp_ids[$m]]
                );
            } catch (PDOException $e) {
                echo "Got " . $e->getMessage();
            }
        }
    }
}

// retrieve new data from form
$new_dates = $_POST['edate'];
$new_methods  = $_POST['emeth'];
$new_amounts = $_POST['eamt'];
$new_payees = $_POST['epay'];
// save the new stuff to the 'Budgets' table
for ($n=0; $n<count($new_payees); $n++) {
    if (!empty($new_payees[$n])) {
        $new = "INSERT INTO `Charges` (`user`,`method`,`expdate`,`expamt`," .
            "`payee`,`recon`) VALUES ('" . $user . "','" . $new_methods[$n] .
            "','" . $new_dates[$n] . "','" . $new_amounts[$n] . 
            "','" . $new_payees[$n] . "','N');";
        $pdo->query($new);
    }
}

$goto = "newBudget.php?pnl=three&user=" . $user;
header("Location: {$goto}");