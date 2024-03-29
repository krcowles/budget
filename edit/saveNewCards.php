<?php
/**
 * This script saves all #cards entries into the 'Cards' table.
 * It collects any changes made to existing data, and inserts new data.
 * It then returns to the newBudget.php page (or exit page if 'Save and
 * Return Later' was chosen).
 * PHP Version 7.3
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require "../database/global_boot.php";

$exit  = filter_input(INPUT_POST, 'exit2') === 'no' ? false : true;

// get all pre-entered data, if any
if (isset($_POST['svdcard'])) {
    $cd_names = $_POST['svdcard'];
    $cd_types = $_POST['svdtype'];
    $deletes  = isset($_POST['cdel']) ? $_POST['cdel'] : false;
    $cd_ids   = $_POST['cdids'];
} else {
    $cd_names = false;
}
if ($cd_names) {
    // delete/update as needed
    if ($deletes) {
        for ($k=0; $k<count($deletes); $k++) {
            $del = "DELETE FROM `Cards` WHERE cdindx = {$deletes[$k]};";
            $pdo->query($del);
        }
    }
    // update Cards table
    for ($m=0; $m<count($cd_names); $m++) {
        if (!$deletes || $deletes && (!in_array($cd_ids[$m], $deletes))) {
            $updte = "UPDATE `Cards` SET `cdname` = ?, `type` = ? " .
                "WHERE `cdindx` = ?;";
            $req = $pdo->prepare($updte);
            try {
                $req->execute([$cd_names[$m], $cd_types[$m], $cd_ids[$m]]);
            } catch (PDOException $e) {
                echo "Got " . $e->getMessage();
            }
        }
    }
}

// retrieve new data from form
$new_cards = $_POST['cname'];
$new_types = $_POST['ctype'];
// save the new stuff to the 'Budgets' table
for ($n=0; $n<count($new_cards); $n++) {
    if (!empty($new_cards[$n])) {
        $new = "INSERT INTO `Cards` (`userid`,`cdname`,`type`) VALUES (?,?,?);";
        $newcds = $pdo->prepare($new);
        $newcds->execute([$_SESSION['userid'], $new_cards[$n], $new_types[$n]]);
    }
}
// get the current stored setup value
$getSetupReq = "SELECT `setup` FROM `Users` WHERE `uid`=?;";
$getSetup = $pdo->prepare($getSetupReq);
$getSetup->execute([$_SESSION['userid']]);
$setupRow = $getSetup->fetch(PDO::FETCH_NUM);
$setup = $setupRow[0];
$setup[1] = '1';
$_SESSION['start'] = $setup;

$status = "UPDATE `Users` SET `setup` = :setup WHERE `uid` = :uid;";
$newstat = $pdo->prepare($status);
$newstat->execute(["setup" => $setup, "uid" => $_SESSION['userid']]);

if (!$exit) {
    $redir = "newBudgetPanels.php?pnl={$setup}";
} else {
    $redir = "../utilities/exitPage.php";
}
header("Location: {$redir}");