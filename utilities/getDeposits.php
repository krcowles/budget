<?php
/**
 * This utility gathers a list of recent deposits made by the user for
 * selection in the 'Undo Deposit' modal.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require "../database/global_boot.php";

$prev60 = time() - (60 * 24 * 60 * 60);
$format = "Y-m-d";
$today = "'" . date($format) . "'";
$compday = "'" . date($format, $prev60) . "'";
//$ted = "SELECT * FROM `Deposits` WHERE `date` BETWEEN {$compday} AND {$today};";
$incdateReq = "SELECT * FROM `Deposits` WHERE `userid` = ? AND " .
    "`otd` = 'Y' AND (`date` BETWEEN {$compday} AND {$today});";
$incdate = $pdo->prepare($incdateReq);
$incdate->execute([$_SESSION['userid']]);
$deps = $incdate->fetchAll(PDO::FETCH_ASSOC);
$list_els = [];
for ($i=0; $i<count($deps); $i++) {
    $el = "<tr><td>&nbsp;<input id='incitem" . $i . "' type='checkbox' />&nbsp;</td>" .
        "<td class='amtright'>" . $deps[$i]['amount'] . "</td>" .
        "<td>{$deps[$i]['date']}</td><td class='descleft'>" .
        $deps[$i]['description'] . "</td><td class='noshow'>" . $deps[$i]['depid'] .
        "</td></tr>";
    array_push($list_els, $el);
}
echo json_encode($list_els);
