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
require "../utilities/timeSetup.php";

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
case 'nmexp':
    $acct = filter_input(INPUT_POST, 'acct_name');
    // 'method' is either 'Check or Draft', or card name:
    $method = filter_input(INPUT_POST, 'method');  
    $amt = filter_input(INPUT_POST, 'amt', FILTER_VALIDATE_FLOAT);
    $payto = filter_input(INPUT_POST, 'payto');
    $cdname = $method;
    // Update the Budgets value for 'Non-Monthlies'
    $item = array_search('Non-Monthlies', $account_names);
    $bal = floatval($current[$item]) - $amt;
    $budupdte = "UPDATE `Budgets` SET `current` = :bal WHERE " .
        "`userid` = :uid AND `budname`='Non-Monthlies';";
    $bud = $pdo->prepare($budupdte);
    $bud->execute([":bal" => $bal, ":uid" => $_SESSION['userid']]);

    // Update Irreg with new balance and month/year paid
    $NMBalReq = "SELECT `funds` FROM `Irreg` WHERE `item`=? AND `userid`=?;";
    $NMBal = $pdo->prepare($NMBalReq);
    $NMBal->execute([$acct, $_SESSION['userid']]);
    $acct_bal_row = $NMBal->fetch(PDO::FETCH_ASSOC);
    $acct_bal = floatval($acct_bal_row['funds']);
    $acct_bal -= $amt;
    $NMUpdateReq = "UPDATE `Irreg` SET `mo_pd`=?,`yr_pd`=?,`funds`=? WHERE " .
        " `item`=? AND `userid`=?;";
    $NMUpdate = $pdo->prepare($NMUpdateReq);
    // add to `Charges` table
    $NMUpdate->execute(
        [$current_month, $thisyear, $acct_bal, $acct, $_SESSION['userid']]
    );
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
        "VALUES (?,?,?,?,?,?,'Non-Monthlies',?);";
    $pdo->prepare($addchg)->execute(
        [$_SESSION['userid'], $dbmethod, $cdname, $dbdate, $amt, $payto, $pd]
    );
    break;
case 'income':
    $newcur = []; // updated balance in account after processing, by account id
    $newfnd = []; // updated amt of account that has been funded, by account id
    $funds = floatval(filter_input(INPUT_POST, 'funds'));
    $deposit_amt = $funds;  // $funds will change later...
    for ($j=0; $j<count($account_names); $j++) {
        // $funded, $budbval, and $curbal are in lock-step wrt/ $account_names
        $funded = floatval($income[$j]);  // amount already funded for this acct
        $budval = floatval($budgets[$j]); // amount budgeted for this acct
        $curbal = floatval($current[$j]); // current balance for this acct
        $item_acctid = $acctid[$j];
        if ($budval == 0) { // includes (at least) Undistributed Funds
            $newfnd[$item_acctid] = (string) 0;
            $newcur[$item_acctid] = (string) $curbal;
        } else if ($funded < $budval) { // more funding needed for account?
            $delta = $budval - $funded; // additional amt to add to acct funding
            if ($funds >= $delta) { // sufficient funds remain
                $newfnd[$item_acctid] = (string) $budval;
                $bal = $curbal + $delta; // new acct balance
                $funds -= $delta;
            } else { // not enough funding left to fully fund this acct
                $newbucks = $funded + $funds; // add what left in funding
                $newfnd[$item_acctid] = (string) $newbucks;
                $bal = $curbal + $funds; // new acct balance
                $funds = 0;
            }
            $newcur[$item_acctid] = (string) $bal;
            if ($funds === 0) {
                break;
            }
        }
    }
    $fndkey = array_keys($newfnd);   // acct ids of updated accts
    $fndval = array_values($newfnd); // updated amt funded of updated acct
    if ($funds > 0) { // any funds left?
        /**
         * Adjust the entry for Undistributed Funds to reflect any post-distribution
         * funds. The account id for Undistributed Funds is needed
         */
        $undis_indx = array_search('Undistributed Funds', $account_names);
        $undis_acct_id = $acctid[$undis_indx];
        $undis_balance = floatval($current[$undis_indx]) + $funds;
        $newcur[$undis_acct_id] = (string) $undis_balance;
    }
    for ($l=0; $l<count($fndkey); $l++) {
        $new_balance = $newcur[$fndkey[$l]];
        $adjinc = "UPDATE `Budgets` SET `current` = :bal," .
            "`funded` = :newfund WHERE `id` = :id;";
        $adjmt = $pdo->prepare($adjinc);
        $adjmt->execute(
            ["bal" => $new_balance, "newfund" => $fndval[$l], "id" => $fndkey[$l]]
        );
    }
    /**
     * Note that Non-Monthlies records are updated during budgetSetup.php,
     * so there is no need to update them here...
     */
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
case 'apmod':
    $apaccount = filter_input(INPUT_POST, 'acct');
    $apmethod  = filter_input(INPUT_POST, 'method');
    $apday     = filter_input(INPUT_POST, 'day');
    $acctindx = array_search($apaccount, $account_names);
    $tblid = $acctid[$acctindx];
    $todays_numeric_day = date('d');
    $apmo = intval($apday) <= intval($todays_numeric_day) ? 
        $numeric_month - 1 : $numeric_month;
    $apmo = $apmo < 10 ? '0' . $apmo : $apmo;
    $sql = "UPDATE `Budgets` SET `autopay` = :ap,`moday` = :moday," .
        "`autopd` = :autopd WHERE `id` = :uid;";
    $apset = $pdo->prepare($sql);
    $apset->execute(
        ["ap" => $apmethod, "moday" => $apday, "autopd" => $apmo,  "uid" => $tblid]
    );
    break;
case 'apset':
    $charged = filter_input(INPUT_POST, 'acct');
    $method  = filter_input(INPUT_POST, 'method');
    $day     = filter_input(INPUT_POST, 'day');
    $pd      = filter_input(INPUT_POST, 'pd');
    $acctindx = array_search($charged, $account_names);
    $apmo = $pd === 'no' ? $numeric_month - 1 : $numeric_month;
    $apmo = $apmo < 10 ? '0' . $apmo : $apmo;    
    $tblid = $acctid[$acctindx];
    $sql = "UPDATE `Budgets` SET `autopay` = :ap,`moday` = :moday, " .
        "`autopd` = :autopd WHERE `id` = :uid;";
    $apset = $pdo->prepare($sql);
    $apset->execute(
        ["ap" => $method, "moday" => $day, "autopd" =>$apmo, "uid" => $tblid]
    );
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
