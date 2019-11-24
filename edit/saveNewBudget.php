<?php
/**
 * This script saves all entries on the new budget into the 'Budgets' table.
 * It then returns to the newBudget.html page with current data.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../database/global_boot.php";

$user = filter_input(INPUT_POST, 'user');
$pos  = filter_input(INPUT_POST, 'lastpos', FILTER_VALIDATE_INT);

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
            try {
                $req->execute(
                    [$chg_accounts[$m], $chg_budgets[$m], 
                    $chg_balances[$m], $chg_ids[$m]]
                );
            } catch (PDOException $e) {
                echo "Got " . $e->getMessage();
            }
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
        $new = "INSERT INTO `Budgets` (`user`,`budname`,`budpos`,`status`," .
        "`budamt`,`prev0`,`prev1`,`current`,`autopay`,`moday`,`autopd`,`funded`) " .
        "VALUES ('" . $user . "','" . $new_accounts[$n] . "','" . $next .
        "','A','" . $new_budgets[$n] . "','0','0','" . $new_balances[$n] .
        "','','0','','0');";
        $pdo->query($new);
        $next++;
    }
}

$return = "newBudget.php?user=" . $user . "&pnl=one";
header("Location: {$return}");
