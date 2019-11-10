<?php
/**
 * This module will make changes to the $budget_data file based on user input:
 * expense items, name change, etc.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../utilities/getBudgetData.php";

// transfer funds:
$xfrfrom   = isset($_GET['from']) ? filter_input(INPUT_GET, 'from') : false;
$xfrto     = isset($_GET['to']) ? filter_input(INPUT_GET, 'to') : false;
$xframt    = isset($_GET['sum']) ? filter_input(INPUT_GET, 'sum') : false;
// move account:
$movefrom  = isset($_GET['mvfrom']) ? filter_input(INPUT_GET, 'mvfrom') : false;
$moveto    = isset($_GET['mvto']) ? filter_input(INPUT_GET, 'mvto') : false;
// onetime deposit:
$newfunds  = isset($_GET['newfunds']) ? filter_input(INPUT_GET, 'newfunds') : false;
// 
$newname   = isset($_GET['newname']) ? filter_input(INPUT_GET, 'newname') : false;
$newbud    = isset($_GET['monthly']) ? filter_input(INPUT_GET, 'monthly') : false;
$acct_name = isset($_GET['acct_name']) ?
    filter_input(INPUT_GET, 'acct_name') : false;
$edit_row  = isset($_GET['edit_row']) ? 
    filter_input(INPUT_GET, 'edit_row', FILTER_VALIDATE_INT) : false;
$chg_type  = isset($_GET['chg_type']) ? filter_input(INPUT_GET, 'chg_type') : false;
$amt       = isset($_GET['amt'])  ? floatval(filter_input(INPUT_GET, 'amt')) : false;
$payee     = isset($_GET['payto']) ? filter_input(INPUT_GET, 'payto') : false;
// delete account
$deletion  = isset($_GET['deletion']) ? filter_input(INPUT_GET, 'deletion') : false;

if ($amt && $chg_type) {
    date_default_timezone_set('America/Denver');
    $date = date("d-M-y");
    $current[$edit_row] = floatval($current[$edit_row]) - $amt;
    $exptype = substr($chg_type, 0, 3);
    if ($exptype === "deb" || $exptype === 'non') {
        // record debit expense
        $dhandle = fopen($expense_log, "w");
        $entry = array($date, $amt, $payee, $acct_name);
        fputcsv($dhandle, $entry);
        fclose($dhandle);
    } elseif ($exptype === "car") {
        // record credit expense
        include "../utilities/getCrData.php";
        $entry = array($acct_name, $date, $payee, $amt);
        array_push($credit_charges[$chg_type], $entry);
        $handle = fopen($credit_data, "w");
        fputcsv($handle, $crHeaders);
        foreach ($credit_charges as $cardset) {
            foreach ($cardset as $line) {
                fputcsv($handle, $line);
            }
        }
        fclose($handle);
    }
}
if ($newname && $acct_name) {
    for ($m=0; $m<count($account_names); $m++) {
        if ($account_names[$m] == $acct_name) {
            $account_names[$m] = $newname;
            break;
        }
    }
}
if ($newbud && $acct_name) {
    // find Undistribute Accounts, and add before that
    for ($n=0; $n<count($account_names); $n++) {
        if ($account_names[$n] === 'Undistributed Funds') {
            $newindx = $n;
            break;
        }
    }
    array_splice($account_names, $newindx, 0, $acct_name);
    array_splice($budgets, $newindx, 0, $newbud);
    array_splice($prev0, $newindx, 0, 0);
    array_splice($prev1, $newindx, 0, 0);
    array_splice($current,  $newindx, 0, 0);
    array_splice($autopay, $newindx, 0, "");
    array_splice($day, $newindx, 0, "");
    array_splice($paid, $newindx, 0, "");
    array_splice($income, $newindx, 0, 0);
}
if ($newfunds) {
    for ($f=0; $f<count($account_names); $f++) {
        if ($account_names[$f] === 'Undistributed Funds') {
            $current[$f] = floatval($current[$f]) + floatval($newfunds);
            break;
        }
    }
}
if ($xfrfrom && $xfrto && $xframt) {
    $xfrval = floatval($xframt);
    for ($i=0; $i<count($account_names); $i++) {
        if ($account_names[$i] == $xfrfrom) {
            $from = $i;
        } elseif ($account_names[$i] == $xfrto) {
            $to = $i;
        }
    }
    $current[$from] = floatval($current[$from]) - $xfrval;
    $current[$to] = floatval($current[$to]) + $xframt;
}
if ($movefrom && $moveto) {
    $newaccts = [];
    $newbuds  = [];
    $newp0    = [];
    $newp1    = [];
    $newcur   = [];
    $newauto  = [];
    $newday   = [];
    $newpd    = [];
    $newinc   = [];
    for ($j=0; $j<count($account_names); $j++) {
        if ($account_names[$j] == $movefrom) {
            $movedrow = $j;
            break;
        }
    }
    for ($k=0; $k<count($account_names); $k++) {
        // delete movedrow first:  
        if ($k === $movedrow) { // note: splice returns ARRAY
            $mvacct = array_splice($account_names, $k, 1);
            $mvbud  = array_splice($budgets, $k, 1);
            $mvp0   = array_splice($prev0, $k, 1);
            $mvp1   = array_splice($prev1, $k, 1);
            $mvcur  = array_splice($current, $k, 1);
            $mvauto = array_splice($autopay, $k, 1);
            $mvday  = array_splice($day, $k, 1);
            $mvpd   = array_splice($paid, $k, 1);
            $mvinc  = array_splice($income, $k, 1);
            break;
        }
    }
    // now get the index in the updated arrays
    for ($q=0; $q<count($account_names); $q++) {
        if ($account_names[$q] == $moveto) {
            $movedto = $q;
            break;
        }
    }
    // fill the new arrays with the old values UP TO $moveto:
    if ($movedto > 0) {
        $newaccts = array_splice($account_names, 0, $movedto);
        $newbuds  = array_splice($budgets, 0, $movedto);
        $newp0    = array_splice($prev0, 0, $movedto);
        $newp1    = array_splice($prev1, 0, $movedto);
        $newcur   = array_splice($current, 0, $movedto);
        $newauto  = array_splice($autopay, 0, $movedto);
        $newday   = array_splice($day, 0, $movedto);
        $newpd    = array_splice($paid, 0, $movedto);
        $newinc   = array_splice($income, 0, $movedto);
    }
    // now add the moved row
    array_push($newaccts, $mvacct[0]);
    array_push($newbuds, $mvbud[0]);
    array_push($newp0, $mvp0[0]);
    array_push($newp1, $mvp1[0]);
    array_push($newcur, $mvcur[0]);
    array_push($newauto, $mvauto[0]);
    array_push($newday, $mvday[0]);
    array_push($newpd, $mvpd[0]);
    array_push($newinc, $mvinc[0]);
    // fill remainder
    for ($p=0; $p<count($account_names); $p++) {
        array_push($newaccts, $account_names[$p]);
        array_push($newbuds, $budgets[$p]);
        array_push($newp0, $prev0[$p]);
        array_push($newp1, $prev1[$p]);
        array_push($newcur, $current[$p]);
        array_push($newauto, $autopay[$p]);
        array_push($newday, $day[$p]);
        array_push($newpd, $paid[$p]);
        array_push($newinc, $income[$p]);
    }
    $account_names = $newaccts;
    $budgets = $newbuds;
    $prev0 = $newp0;
    $prev1 = $newp1;
    $current = $newcur;
    $autopay = $newauto;
    $day = $newday;
    $paid = $newpd;
    $income = $newinc;
}
if ($deletion) {
    for ($v=0; $v<count($account_names); $v++) {
        if ($account_names[$v] == $deletion) {
            $delrow = $v;
            break;
        }
    }
    for ($k=0; $k<count($account_names); $k++) {
        // delete delrow 
        if ($k === $delrow) { // note: splice returns ARRAY
            array_splice($account_names, $k, 1);
            array_splice($budgets, $k, 1);
            array_splice($prev0, $k, 1);
            array_splice($prev1, $k, 1);
            array_splice($current, $k, 1);
            array_splice($autopay, $k, 1);
            array_splice($day, $k, 1);
            array_splice($paid, $k, 1);
            array_splice($income, $k, 1);
            break;
        }
    }
    $x =1;
}

// write budget data back out
$handle = fopen($budget_data, "w");
fputcsv($handle, $headers);
for ($i=0; $i<count($account_names); $i++) {
    $line = array($account_names[$i], $budgets[$i], $prev0[$i], $prev1[$i],
        $current[$i], $autopay[$i], $day[$i], $paid[$i], $income[$i]);
    fputcsv($handle, $line);
}
fclose($handle);
echo "OK";
