<?php
/**
 * This script extracts the user's budget data from the 'Budgets' table
 * The data is compiled in arrays for consumption by the caller. Also, 
 * html select drop-downs are formed for use in some of the modals which
 * request user input.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license
 */
require_once "../database/global_boot.php";
// output arrays:
$account_names = [];
$positions = [];
$budgets = [];
$prev0 = [];
$prev1 = [];
$current = [];
$autopay = [];
$day = [];
$paid = [];
$income = [];
$request = "SELECT * FROM Budgets WHERE user = :user AND status = 'A';";
$stmnt = $pdo->prepare($request);
$stmnt->execute(["user" => $user]);
$bud_dat = $stmnt->fetchALL(PDO::FETCH_ASSOC);
if (count($bud_dat) === 0) {
    // go to budget setup
} else {
    foreach ($bud_dat as $acct) {
        array_push($account_names, $acct['budname']);
        array_push($positions, $acct['budpos']);
        $amt = empty($acct['budamt']) ? 0 : $acct['budamt'];
        array_push($budgets, $amt);
        $p0 = empty($acct['prev0']) ? 0 : $acct['prev0'];
        array_push($prev0, $p0);
        $p1 = empty($acct['prev1']) ? 0 : $acct['prev1'];
        array_push($prev1, $p1);
        $curr = empty($acct['current']) ? 0 : $acct['current'];
        array_push($current, $curr);
        $ap = empty($acct['autopay']) ? '' : $acct['autopay'];
        array_push($autopay, $ap);
        $moday = empty($acct['moday']) ? 0 : $acct['moday'];
        array_push($day, $moday);
        $apd = empty($acct['autopd']) ? '' : $acct['autopd'];
        array_push($paid, $apd);
        $inc = empty($acct['funded']) ? 0 : $acct['funded'];
        array_push($income, $inc);
    }
}
// copy the arrays so that they can be re-sequenced according to $positions
$nmeArray = new ArrayObject($account_names);
$budArray = new ArrayObject($budgets);
$p0Array  = new ArrayObject($prev0);
$p1Array  = new ArrayObject($prev1);
$curArray = new ArrayObject($current);
$apArray  = new ArrayObject($autopay);
$dayArray = new ArrayObject($day);
$pdArray  = new ArrayObject($paid);
$incArray = new ArrayObject($income);
for ($i=0; $i<count($account_names); $i++) {
    $posval = $i + 1; // there is no 0 position
    $pos = array_search($posval, $positions); // find the key for each consec. item
    $account_names[$i] = $nmeArray[$pos];
    $budgets[$i] = $budArray[$pos];
    $prev0[$i] = $p0Array[$pos];
    $prev1[$i] = $p1Array[$pos];
    $current[$i] = $curArray[$pos];
    $autopay[$i] = $apArray[$pos];
    $day[$i] = $dayArray[$pos];
    $paid[$i] = $pdArray[$pos];
    $income[$i] = $incArray[$pos];
}
$user_cnt = count($account_names);
// get the temporary accounts (includes Undistributed Funds)
$tnmes = [];
$tbud = [];
$tp0 = [];
$tp1 = [];
$tcur = [];
$tap = [];
$tday = [];
$tpd = [];
$tinc = [];
$treq = "SELECT * FROM Budgets WHERE `user` = :user AND `status` = 'T';";
$tdat = $pdo->prepare($treq);
$tdat->execute(["user" => $user]);
$temps = $tdat->fetchALL(PDO::FETCH_ASSOC);
foreach ($temps as $tacct) {
    array_push($tnmes, $tacct['budname']);
    $tb = empty($tacct['budamt']) ? 0 : $tacct['budamt'];
    array_push($tbud, $tb);
    $t0 = empty($tacct['prev0']) ? 0 : $tacct['prev0'];
    array_push($tp0, $t0);
    $t1 = empty($tacct['prev1']) ? 0 : $tacct['prev1'];
    array_push($tp1, $t1);
    $tc = empty($tacct['current']) ? 0 : $tacct['current'];
    array_push($tcur, $tc);
    $ta = empty($tacct['autopay']) ? '' : $tacct['autopay'];
    array_push($tap, $ta);
    $td = empty($tacct['moday']) ? 0 : $tacct['moday'];
    array_push($tday, $td);
    $tp = empty($tacct['autopd']) ? '' : $tacct['autopd'];
    array_push($tpd, $tp);
    $ti = empty($tacct['funded']) ? 0 : $tacct['funded'];
    array_push($tinc, $ti);
}
$account_names = array_merge($account_names, $tnmes);
$budgets       = array_merge($budgets, $tbud);
$prev0         = array_merge($prev0, $tp0);
$prev1         = array_merge($prev1, $tp1);
$current       = array_merge($current, $tcur);
$autopay       = array_merge($autopay, $tap);
$day           = array_merge($day, $tday);
$paid          = array_merge($paid, $tpd);
$income        = array_merge($income, $tinc);
// create html for <select> drop-downs
$fullsel = '<select class="fullsel">';
for ($k=0; $k<count($account_names); $k++) {
    $fullsel .= '<option value="' . $account_names[$k] . '">' .
        $account_names[$k] . '</option>';
}
$fullsel .= '</select>';
$partsel = '<select class="partsel">';
for ($n=0; $n<$user_cnt; $n++) {
    $partsel .= '<option value="' . $account_names[$n] . '">' .
        $account_names[$n] . '</option>';
}
$partsel .= '</select>';