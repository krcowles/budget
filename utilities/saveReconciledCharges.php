<?php
/**
 * This module will write new charges out, deleting those which have been
 * reconciled by the user.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
$user = filter_input(INPUT_POST, 'user'); // mandatory prior to 'requires'
$subj = filter_input(INPUT_POST, 'card');

require "../utilities/getCards.php";
require "../utilities/getExpenses.php";

$cardset = [];
for ($i=0; $i<count($expamt); $i++) {
    if ($expcdname[$i] === $subj) {
        array_push($cardset, $expid[$i]);
    }
}
$paid = isset($_POST['del']) ? $_POST['del'] : false;
if ($paid) {
    $pdindx = 0;
    for ($j=0; $j<count($cardset); $j++) {
        if ($paid[$pdindx] == $cardset[$j]) {
            $payit = "UPDATE `Charges` SET `paid` = 'Y' WHERE `expid` = :id;";
            $updte = $pdo->prepare($payit);
            $updte->execute(["id" => $paid[$pdindx]]);
            $pdindx++;
            if ($pdindx >= count($paid)) {
                break;
            }
        }
    }
}

// encode card name as it may contain spaces
$return = "reconcile.php?user=" . $user . "&card=" . urlencode($subj);
header("Location: {$return}");
