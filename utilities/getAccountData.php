<?php
/**
 * This script extracts the user's budget data from the 'Budgets' table.
 * The data is compiled in arrays for consumption by the caller. Also, 
 * html select drop-downs are formed for use in some of the modals which
 * may request user input.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license
 */
require_once "../database/global_boot.php";
// output arrays:
$acctid = [];
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
$request = "SELECT * FROM `Budgets` WHERE `userid` = :uid AND status = 'A';";
$stmnt = $pdo->prepare($request);
$stmnt->execute(["uid" => $_SESSION['userid']]);
$bud_dat = $stmnt->fetchALL(PDO::FETCH_ASSOC);
if (count($bud_dat) === 0) {
    // go to budget setup
} else {
    foreach ($bud_dat as $acct) {
        array_push($acctid, $acct['id']);
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
$idArrayObj  = new ArrayObject($acctid);
$idArray = $idArrayObj->getArrayCopy();
$nmeArrayObj = new ArrayObject($account_names);
$nmeArray = $nmeArrayObj->getArrayCopy();
$posArrayObj = new ArrayObject($positions);
$posArray = $posArrayObj->getArrayCopy();
$budArrayObj = new ArrayObject($budgets);
$budArray = $budArrayObj->getArrayCopy();
$p0ArrayObj  = new ArrayObject($prev0);
$p0Array = $p0ArrayObj->getArrayCopy();
$p1ArrayObj  = new ArrayObject($prev1);
$p1Array = $p1ArrayObj->getArrayCopy();
$curArrayObj = new ArrayObject($current);
$curArray = $curArrayObj->getArrayCopy();
$apArrayObj  = new ArrayObject($autopay);
$apArray = $apArrayObj->getArrayCopy();
$dayArrayObj = new ArrayObject($day);
$dayArray = $dayArrayObj->getArrayCopy();
$pdArrayObj  = new ArrayObject($paid);
$pdArray = $pdArrayObj->getArrayCopy();
$incArrayObj = new ArrayObject($income);
$incArray = $incArrayObj->getArrayCopy();
$orgPosObj = new ArrayObject($positions);
$orgpos = $orgPosObj->getArrayCopy();
asort($positions);
$keyindx = 0;
foreach ($positions as $sorted) {
    $pos = array_search($sorted, $orgpos); // get positional key
    $acctid[$keyindx] = $idArray[$pos];
    $positions[$keyindx] = $posArray[$pos];
    $account_names[$keyindx] = $nmeArray[$pos];
    $budgets[$keyindx] = $budArray[$pos];
    $prev0[$keyindx] = $p0Array[$pos];
    $prev1[$keyindx] = $p1Array[$pos];
    $current[$keyindx] = $curArray[$pos];
    $autopay[$keyindx] = $apArray[$pos];
    $day[$keyindx] = $dayArray[$pos];
    $paid[$keyindx] = $pdArray[$pos];
    $income[$keyindx] = $incArray[$pos];
    $keyindx++;
}
$user_cnt = count($account_names);
// get the temporary accounts (includes Undistributed Funds)
$tid = [];
$tnmes = [];
$tbud = [];
$tp0 = [];
$tp1 = [];
$tcur = [];
$tap = [];
$tday = [];
$tpd = [];
$tinc = [];
$treq = "SELECT * FROM `Budgets` WHERE `userid` = :uid AND `status` = 'T' " .
    "ORDER BY `budpos`;";
$tdat = $pdo->prepare($treq);
$tdat->execute(["uid" => $_SESSION['userid']]);
$temps = $tdat->fetchALL(PDO::FETCH_ASSOC);
foreach ($temps as $tacct) {
    array_push($tid, $tacct['id']);
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
$acctid        = array_merge($acctid, $tid);
$account_names = array_merge($account_names, $tnmes);
$budgets       = array_merge($budgets, $tbud);
$prev0         = array_merge($prev0, $tp0);
$prev1         = array_merge($prev1, $tp1);
$current       = array_merge($current, $tcur);
$autopay       = array_merge($autopay, $tap);
$day           = array_merge($day, $tday);
$paid          = array_merge($paid, $tpd);
$income        = array_merge($income, $tinc);
// if there is a 'Deferred Income' budget:
$deferred_amount = '0';
if ($key = array_search('Deferred Income', $account_names)) {
    $deferred_amount = $current[$key];
}
// create html for <select> drop-downs
$fullsel = '<select class="fullsel">';
for ($k=0; $k<count($account_names); $k++) {
    if ($k === 0) {
        $fullsel .= '<option value="' . $account_names[$k] . 
            '" selected>' . $account_names[$k] . '</option>';
    } else {
        $fullsel .= '<option value="' . $account_names[$k] . '">' .
            $account_names[$k] . '</option>';
    }
}
$fullsel .= '</select>';
$partsel = '<select class="partsel">';
for ($n=0; $n<$user_cnt; $n++) {
    if ($n === 0) {
        $partsel .= '<option value="' . $account_names[$n] .
            '" selected>' . $account_names[$n] . '</option>';
    } else {
        $partsel .= '<option value="' . $account_names[$n] . '">' .
            $account_names[$n] . '</option>';
    }
}
$partsel .= '</select>';
