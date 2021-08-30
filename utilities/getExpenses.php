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
$date_element = [];
$chgreq = "SELECT * FROM `Charges` WHERE `userid` = :uid AND `paid` = 'N';";
$chgdat = $pdo->prepare($chgreq);
$chgdat->execute(["uid" => $_SESSION['userid']]);
$charges = $chgdat->fetchALL(PDO::FETCH_ASSOC);
$expenses = count($charges) > 0 ? true : false;
// my brainless 'date-sort' algorithm
if ($expenses) {
    // set up an associative array so a sort-retaining-key can be made
    for ($k=0; $k<count($charges); $k++) {
        $indx = 'indx' . $k;
        $date_element[$indx] = $charges[$k]['expdate'];   
    }
    asort($date_element); // retains associative keys
    $date_keys = array_keys($date_element);
    $order = [];
    foreach ($date_keys as $indx) {
        $item_no = intval(substr($indx, 4));
        array_push($order, $item_no); 
    }
    // $order is the sorted index for all charges
    for ($j=0; $j<count($charges); $j++) {
        $i = $order[$j];
        array_push($expid, $charges[$i]['expid']);
        array_push($expamt, $charges[$i]['expamt']);
        array_push($expdate, $charges[$i]['expdate']);
        array_push($expmethod, $charges[$i]['method']);
        array_push($expcdname, $charges[$i]['cdname']);
        array_push($exppayee, $charges[$i]['payee']);
        array_push($expcharged, $charges[$i]['acctchgd']);
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
