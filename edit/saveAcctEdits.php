<?php
/**
 * This module will make changes to the data in the 'Budgets' table,
 * file based on user input: expense items, name change, etc.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require_once "../database/global_boot.php";
require "../utilities/getAccountData.php";
require "../utilities/getCards.php";

$id = filter_input(INPUT_POST, 'id');

date_default_timezone_set('America/Denver');
$dbdate = date("Y-m-d");
// get current highest budpos for user:
$budposReq = "SELECT `budpos` FROM `Budgets` WHERE `userid` = :uid AND " .
    "`status` = 'A' ORDER BY 1 DESC LIMIT 1;";
$highbud = $pdo->prepare($budposReq);
$highbud->execute(["uid" => $_SESSION['userid']]);
$budnos = $highbud->fetch(PDO::FETCH_ASSOC);
$user_cnt = $budnos['budpos'];

switch ($id) {
case 'payexp':
    $acct = filter_input(INPUT_POST, 'acct_name');
    // 'method' is either 'Check or Draft', or card name:
    $method = filter_input(INPUT_POST, 'method');  
    $amt = filter_input(INPUT_POST, 'amt');
    $payto = filter_input(INPUT_POST, 'payto');
    $cdname = $method;
    $item = array_search($acct, $account_names);
    $bal = floatval($current[$item]) - floatval($amt);
    $budupdte = "UPDATE `Budgets` SET `current` = :bal WHERE " .
        "`userid` = :uid AND `budname` = :acct;";
    $bud = $pdo->prepare($budupdte);
    $bud->execute([":bal" => $bal, ":uid" => $_SESSION['userid'], ":acct" => $acct]);
    // examine $method as either  Cr or Dr card name
    if (in_array($method, $cr)) { // a credit card
        $dbmethod = "Credit";
        $pd = 'N';
    } elseif (in_array($method, $dr)) { // a debit card
        $dbmethod = "Debit";
        $pd = 'Y';
    } else { // 'Check or Draft'
        $dbmethod = "Check";
        $pd = 'Y';
    }
    $addchg = "INSERT INTO `Charges` (`userid`, `method`, `cdname`," .
        "`expdate`, `expamt`, `payee`, `acctchgd`, `paid`) " .
        "VALUES (?,?,?,?,?,?,?,?);";
    $pdo->prepare($addchg)->execute(
        [
            $_SESSION['userid'], 
            $dbmethod,
            $cdname,
            $dbdate,
            $amt,
            $payto,
            $acct,
            $pd
        ]
    );
    break;
case 'income':
    $newcur = [];
    $newfnd = [];
    $funds = floatval(filter_input(INPUT_POST, 'funds'));
    $deposit_amt = $funds;
    $indx = array_search('Undistributed Funds', $account_names);
    for ($j=0; $j<count($account_names); $j++) {
        $funded = floatval($income[$j]);
        $budval = floatval($budgets[$j]);
        $curbal = floatval($current[$j]);
        if ($funded < $budval) {
            $delta = $budval - $funded;
            if ($funds >= $delta) {
                $fnd = array((string) $acctid[$j] => (string) $budval);
                array_push($newfnd, $fnd);
                $bal = $curbal + $delta;
                array_push($newcur, (string) $bal);
                $funds -= $delta;
            } else {
                $newbucks = $funded + $funds;
                $fnd = array((string) $acctid[$j] => (string) $newbucks);
                array_push($newfnd, $fnd);
                $bal = $curbal + $funds;
                array_push($newcur, (string) $bal);
                $funds = 0;
                break;
            }
        }
    }
    // $newfnd is an array of arrays, thus cannot use fct array_values()
    $fndval = [];
    $fndkey = [];
    for ($q=0; $q<count($newfnd); $q++) {
        // this is the item's unique table `id`
        $fndkey[$q] = (string) key($newfnd[$q]); 
        // this is the new value for `funded`
        $fndval[$q] = $newfnd[$q][$fndkey[$q]];  
    }
    // Note: $newbal has the same indices and does not need to be an array of arrays
    if ($funds > 0) {
        $uinc = floatval($current[$indx]) + $funds;
        array_push($newcur, (string) $uinc);
        array_push($fndval, '0');
        array_push($fndkey, $acctid[$indx]); // unique id for this Undist acct
    }
    for ($l=0; $l<count($fndkey); $l++) {
        $adjinc = "UPDATE `Budgets` SET `current` = :bal," .
            "`funded` = :newfund WHERE `id` = :id;";
        $adjmt = $pdo->prepare($adjinc);
        $adjmt->execute(
            ["bal" => $newcur[$l], "newfund" => $fndval[$l], "id" => $fndkey[$l]]
        );
    }
    // Record the deposit
    $depositReq = "INSERT INTO `Deposits` (`userid`,`date`,`amount`,`otd`," .
        "`description`) VALUES (?,?,?,'N','');";
    $deposit = $pdo->prepare($depositReq);
    $deposit->execute([$_SESSION['userid'], $dbdate, $deposit_amt]);
    break;
case 'otdeposit':
    $funds = filter_input(INPUT_POST, 'newfunds');
    $note  = filter_input(INPUT_POST, 'note');
    $key = array_search('Undistributed Funds', $account_names);
    $newval = floatval($current[$key]) + floatval($funds);
    $undis = (string) $newval;
    $loc = (string) $acctid[$key];
    $updte = "UPDATE `Budgets` SET `current` = :undis WHERE `id` = :tblid;";
    $newundis = $pdo->prepare($updte);
    $newundis->execute(["undis" => $undis, "tblid" => $loc]);
    // record in 'Deposits' table
    $depositReq = "INSERT INTO `Deposits` (`userid`,`date`,`amount`,`otd`," .
        "`description`) VALUES (?,?,?,'Y',?);";
    $deposit = $pdo->prepare($depositReq);
    $deposit->execute(
        [$_SESSION['userid'], $dbdate, $funds, $note]
    );
    break;
case 'xfr':
    $from = filter_input(INPUT_POST, 'from');
    $to   = filter_input(INPUT_POST, 'to');
    $amt  = filter_input(INPUT_POST, 'sum');
    $fromkey = array_search($from, $account_names);
    $frmid = $acctid[$fromkey];
    $tokey   = array_search($to, $account_names);
    $toid  = $acctid[$tokey]; 
    $xfrout = floatval($current[$fromkey]) - floatval($amt);
    $current[$fromkey] = (string) $xfrout; 
    $xfrin  = floatval($current[$tokey]) + floatval($amt);
    $current[$tokey]   = (string) $xfrin;
    $frmreq = "UPDATE `Budgets` SET `current` = :frm WHERE `id` = :frmid;";
    $frmq = $pdo->prepare($frmreq);
    $frmq->execute(["frm" => $xfrout, "frmid" => $frmid]);
    $toreq = "UPDATE `Budgets` SET `current` = :toc WHERE `id` = :toid;";
    $toq  = $pdo->prepare($toreq);
    $toq->execute(["toc" => $xfrin, "toid" => $toid]);
    break;
case 'apset':
    $charged = filter_input(INPUT_POST, 'acct');
    $method  = filter_input(INPUT_POST, 'method');
    $day     = filter_input(INPUT_POST, 'day');
    $acctindx = array_search($charged, $account_names);
    $tblid = $acctid[$acctindx];
    $sql = "UPDATE `Budgets` SET `autopay` = :ap,`moday` = :moday " .
        "WHERE `id` = :uid;";
    $apset = $pdo->prepare($sql);
    $apset->execute(["ap" => $method, "moday" => $day, "uid" => $tblid]);
    break;
case 'delapay':
    $delacct = filter_input(INPUT_POST, 'acct');
    $namekey = array_search($delacct, $account_names);
    $tblid = $acctid[$namekey];
    $delauto = "UPDATE `Budgets` SET `autopay` = '', `moday` = '0', " .
        "`autopd` = '' WHERE `id` = :uid;";
    $da = $pdo->prepare($delauto);
    $da->execute(["uid" => $tblid]);
    break;
case 'addcd':
    $newcard = filter_input(INPUT_POST, 'cdname');
    $newtype = filter_input(INPUT_POST, 'cdtype');
    $cdsql = "INSERT INTO `Cards` (`userid`, `cdname`, `type`) " .
        "Values (:uid, :name, :type);";
    $newcdentry = $pdo->prepare($cdsql);
    $newcdentry->execute(
        ["uid" => $_SESSION['userid'], "name" => $newcard, "type" => $newtype]
    );
    break;
case 'decard':
    $cd2delete = filter_input(INPUT_POST, 'target');
    $delsql = "DELETE FROM `Cards` WHERE `userid` = :uid AND `cdname` = :cd;";
    $delstmnt = $pdo->prepare($delsql);
    $delstmnt->execute(["uid" => $_SESSION['userid'], "cd" => $cd2delete]);
    break;
case 'addacct':
    $newacct = filter_input(INPUT_POST, 'acct_name');
    $budget  = filter_input(INPUT_POST, 'monthly');
    $newpos = $user_cnt + 1;
    $newsql = "INSERT INTO `Budgets` (`user`,`userid`,`budname`,`budpos`," .
        "`status`,`budamt`,`prev0`,`prev1`,`current`,`autopay`,`moday`," .
        "`autopd`,`funded`) VALUES (:user,:uid,:item,:pos,'A',:amt,'0','0'," .
        "'0','','0','','0');";
    $addinfo = $pdo->prepare($newsql);
    $pass = $addinfo->execute(
        [
            ":user" => 'Not used',
            ":uid"  => $_SESSION['userid'],
            ":item" => $newacct,
            ":pos"  => $newpos,
            ":amt"  => $budget
        ]
    ); 
    break;
case 'acctdel':
    $type   = filter_input(INPUT_POST, 'type');
    $target = filter_input(INPUT_POST, 'acct');
    $delreq = "DELETE FROM `Budgets` WHERE `budname` = :bud AND `userid` = :uid;";
    $delbud = $pdo->prepare($delreq);
    $delbud->execute(["bud" => $target, "uid" => $_SESSION['userid']]);
    if ($type === 'def') {
        // if deleting Deferred Income, clear out 'definc' in Users
        $updateDefincReq = "UPDATE `Users` SET `definc` = ? WHERE `uid` = ?;";
        $updateDefinc = $pdo->prepare($updateDefincReq);
        $updateDefinc->execute(['', $_SESSION['userid']]);
    }
    break;
case 'move':
    // $positions array holds only positions of user-created accounts
    $lastpos = max($positions); // account for position of 'Undistributed Funds'
    // 'Undistributed Funds' is not normally in the user array, so add a position:
    array_push($positions, $lastpos+1);
    $move = filter_input(INPUT_POST, 'mvfrom');
    $above = filter_input(INPUT_POST, 'mvto');
    $tokey = array_search($above, $account_names);
    $frmkey = array_search($move, $account_names);
    $totblid = $acctid[$tokey];
    $frmtblid = $acctid[$frmkey];
    if ($frmkey > $tokey) {
        $chgpos = $positions[$tokey];
        $newpos = "UPDATE `Budgets` SET `budpos` = :pos WHERE `id` = :tid;";
        $newplace = $pdo->prepare($newpos);
        $newplace->execute(["pos" => $chgpos, "tid" => $frmtblid]);
        for ($j=$tokey; $j<$user_cnt; $j++) {
            if ($move !== $account_names[$j]) {
                $chgpos++; 
                $tblid = $acctid[$j];
                $updtepos = "UPDATE `Budgets` SET `budpos` = :pos " .
                    "WHERE `id` = :tblid";
                $updt = $pdo->prepare($updtepos);
                $updt->execute(["pos" => $chgpos, "tblid" => $acctid[$j]]);
            }
        }
    } else {
        $start = $frmkey + 1;
        $mvdpos = $positions[$tokey] - 1;
        $firstmv = "UPDATE `Budgets` SET `budpos` = :pos " .
            "WHERE `id` = :tblid;";
        $fmv = $pdo->prepare($firstmv);
        $fmv->execute(["pos" => $mvdpos, "tblid" => $frmtblid]);
        for ($m=$start; $m<$tokey; $m++) {
            $decr = $positions[$m] - 1;
            $dec = "UPDATE `Budgets` SET `budpos` = :pos " .
                "WHERE `id` = :itemid;";
            $decpos = $pdo->prepare($dec);
            $decpos->execute(["pos" => $decr, "itemid" => $acctid[$m]]);
        }
    }
    break;
case 'rename':
    $target = filter_input(INPUT_POST, 'acct');
    $name   = filter_input(INPUT_POST, 'newname');
    $acctkey = array_search($target, $account_names);
    $tblid = $acctid[$acctkey];
    $nmesql = "UPDATE `Budgets` SET `budname` = :bnme WHERE `id` = :uid;";
    $renme = $pdo->prepare($nmesql);
    $renme->execute(["bnme" => $name, "uid" => $tblid]);
    break;
}
echo "OK";
