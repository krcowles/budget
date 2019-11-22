<?php
/**
 * To get it going...
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Tom Sandberg and Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
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
/*
$lead = "INSERT INTO Charges (`user`,`method`,`expdate`,`expamt`,`payee`,`recon`) ";
$d1 = "VALUES ('krc','Credit','2019-01-10',24.12,'Fred','N');";
$d2 = "VALUES ('krc','Credit','2019-04-04',16.55,'Timbob Potato','N');";
$d3 = "VALUES ('krc','Check','2019-07-10',194,'Sweetpea','N');";
$d4 = "VALUES ('krc','Credit','2019-10-10',86.44,'Nobody I know','N');";
$d5 = "VALUES ('krc','Debit','2019-11-24',1.02,'Clyde C Beatty','N');";
//$pdo->query($lead . $d2);
$pdo->query($lead . $d3);
$pdo->query($lead . $d4);
$pdo->query($lead . $d5);
*/
