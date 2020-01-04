<?php
/**
 * This script will move charge from one location (card or expense) to another.
 * PHP Version 7.1
 * 
 * @package BUDGET
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
$user = filter_input(INPUT_GET, 'user');
$type = filter_input(INPUT_GET, 'type');
$from = filter_input(INPUT_GET, 'frm');
$to   = filter_input(INPUT_GET, 'to');
require "../utilities/getCards.php";

// extract the 'from' data
$fromdat = "SELECT * FROM `Charges` WHERE `expid` = :eid;";
$data = $pdo->prepare($fromdat);
$data->execute(["eid" => $from]);
$fromdata = $data->fetch(PDO::FETCH_ASSOC); // only one entry per expid

switch ($type) {
case 'e2c':  // from type 'expense' to type 'credit'
    $cdname = $cr[intval($to)];
    $addcr = "INSERT INTO `Charges` (`user`,`method`,`cdname`,`expdate`,`expamt`," .
        "`payee`,`acctchgd`,`paid`) " .
        "VALUES (:usr,'Credit',:cd,:dte,:amt,:pay,:acct,'N');";
    $add = $pdo->prepare($addcr);
    $add->execute(
        ["usr" => $user, "cd" => $cdname, "dte" => $fromdata['expdate'],
        "amt" => $fromdata['expamt'], "pay" => $fromdata['payee'],
        "acct" => $fromdata['acctchgd']]
    );
    break;
case 'c2c': // from type 'credit' card to type 'credit' card
    $cdname = $cr[intval($to)];
    $addcr = "INSERT INTO `Charges` (`user`,`method`,`cdname`,`expdate`,`expamt`," .
        "`payee`,`acctchgd`,`paid`) " .
        "VALUES (:usr,'Credit',:cd,:dte,:amt,:pay,:acct,'N');";
    $add = $pdo->prepare($addcr);
    $add->execute(
        ["usr" => $user, "cd" => $cdname, "dte" => $fromdata['expdate'],
        "amt" => $fromdata['expamt'], "pay" => $fromdata['payee'],
        "acct" => $fromdata['acctchgd']]
    );
    break;
case 'c2e': // from type credit card to expense or debit
    if ($to === 'check') {
        $method = 'Check';
        $cdname = '';
    } else {
        $method = 'Debit';
        $cdname = $dr[intval($to)];
    }
    $addexp = "INSERT INTO `Charges` (`user`,`method`,`cdname`,`expdate`,`expamt`," .
        "`payee`,`acctchgd`,`paid`) " .
        "VALUES (:usr,:meth,:cd,:dte,:amt,:pay,:acct,'Y');";
    $add = $pdo->prepare($addexp);
    $add->execute(
        ["usr" => $user, "meth" => $method, "cd" => $cdname,
            "dte" => $fromdata['expdate'], "amt" => $fromdata['expamt'],
            "pay" => $fromdata['payee'], "acct" => $fromdata['acctchgd']]
    );
    $add = $pdo->query($addexp);
    break;
}
// delete the 'from' data
$del = "DELETE FROM `Charges` WHERE `expid` = :eid;";
$delexp = $pdo->prepare($del);
$delexp->execute(["eid" => $from]);
$viewer = "../utilities/viewCharges.php?user=" . rawurlencode($user);
header("Location: {$viewer}");
