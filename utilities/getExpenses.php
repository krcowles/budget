<?php
/**
 * This utility queries the 'Charges' table to extract unpaid expenses
 * (including credit charges not yet reconciled) and store them in arrays
 * for consumption by the caller.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require_once "../database/global_boot.php";
$expid = [];
$expdate = [];
$expamt = [];
$expmethod = [];
$expcdname = [];
$exppayee = [];
$expcharged = [];
$chgreq = "SELECT * FROM `Charges` WHERE `user` = :user AND `paid` = 'N';";
$chgdat = $pdo->prepare($chgreq);
$chgdat->execute(["user" => $user]);
$charges = $chgdat->fetchALL(PDO::FETCH_ASSOC);
$expenses = count($charges) > 0 ? true : false;
if ($expenses) {
    foreach ($charges as $expense) {
        array_push($expid, $expense['expid']);
        array_push($expamt, $expense['expamt']);
        array_push($expdate, $expense['expdate']);
        array_push($expmethod, $expense['method']);
        array_push($expcdname, $expense['cdname']);
        array_push($exppayee, $expense['payee']);
        array_push($expcharged, $expense['acctchgd']);
    }
}

// get credit card outstanding balances:
$cardbal = [];
for ($j=0; $j<count($cr); $j++) {
    $cardbal[$j] = ['name' => $cr[$j], 'bal' => 0];
}
for ($i=0; $i<count($expamt); $i++) {
    if ($expmethod[$i] === 'Credit') {
        // does a card exist for this?
        if (in_array($expcdname[$i], $cr)) {
            for ($k=0; $k<count($cr); $k++) {
                if ($expcdname[$i] == $cardbal[$k]['name']) {
                    $cardbal[$k]['bal'] += $expamt[$i];
                }
            }
        } else {
            echo "Matching card for expense in 'Charges' not found" .
                "Card name " . $expcdname[$i];
        }
    }
}
