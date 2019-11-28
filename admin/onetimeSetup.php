<?php
/**
 * To get it going...
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Tom Sandberg and Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
//require_once "database/global_boot.php";
/*
for ($i=0; $i<count($account_names); $i++) {
    $pos = $i + 1;
    $data = "INSERT INTO Budgets (`user`,`budname`,`budpos`,`status`,`budamt`)" .
        "VALUES ('krc'," . "'" . $account_names[$i] . "',$pos,'A'," .
        "$budgets[$i]);";
    try {
        $pdo->query($data);
    } catch (PDOException $e) {
        echo $e->getMessage() . "; " . (int)$e->getCode();
    }
}

for ($j=0; $j<count($cards); $j++) {
    $cdat = "INSERT INTO Cards (`user`,`cdname`,`type`) VALUES (" .
        "'krc','" . $cards[$j] . "','Credit');";
    $pdo->query($cdat);
}
*/

$lead = "INSERT INTO `Charges` (`user`,`method`,`cdname`,`expdate`,`expamt`," .
    "`payee`,`acctchgd`,`paid`) ";
$d1 = $lead . "VALUES ('krc','Credit','Visa','2019-01-10','24.12','Fred'," .
    "'Tiny Tim','N');";
$d2 = $lead . "VALUES ('krc','Credit','Fred','2019-04-04','16.55','Timbob Potato'," .
    "'Simply Great One','N');";
$d3 = $lead . "VALUES ('krc','Check','','2019-07-10','194','Sweetpea'," .
    "'Clyde C Beatty','N');";
$d4 = $lead . "VALUES ('krc','Credit','Citi','201-10-10','86.44','Nobody I know'," .
    "'Tiny Tim','N');";
$d5 = $lead . "VALUES ('krc','Debit','Wells Fargo','2019-11-24','1.02'," .
    "'Clyde C Beatty','Freddy and Tim','N');";
try {
    $pdo->query($d1);
} catch (PDOException $e) {
    echo "Bad: " . $e->getMessage();
}
$pdo->query($d2);
$pdo->query($d3);
$pdo->query($d4);
$pdo->query($d5);
