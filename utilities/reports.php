<?php
/**
 * This utility will generate a monthly or annual report, where the user
 * specifies the parameters. All expenses, paid or not, will be displayed.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require "../database/global_boot.php";
require "getCards.php";
require "timeSetup.php";

$id = isset($_GET['id']) ? filter_input(INPUT_GET, 'id') : false;
$monthly = $id === 'morpt' ? true : false;
$annual  = $id === 'yrrpt' ? true : false;

$datareq = "SELECT * FROM `Charges` WHERE `userid` = :uid;";
$data = $pdo->prepare($datareq); 
$data->execute(["uid" => $_SESSION['userid']]);
$report_data = $data->fetchALL(PDO::FETCH_ASSOC);
$method = [];
$cdname = [];
$date = [];
$amt = [];
$payee = [];
$acct = [];
$paid = [];
if ($monthly) {
    $period = isset($_GET['mo']) ? filter_input(INPUT_GET, 'mo') : false;
    $mon = array_search($period, $month_names) + 1;
    foreach ($report_data as $item) {
        $expdate = explode("-", $item['expdate']);
        if ($expdate[0] === $digits[2] && $expdate[1] == $mon) {
            array_push($method, $item['method']);
            array_push($cdname, $item['cdname']);
            array_push($date, $item['expdate']);
            array_push($amt, $item['expamt']);
            array_push($payee, $item['payee']);
            array_push($acct, $item['acctchgd']);
            array_push($paid, $item['paid']);
        }
    }
} elseif ($annual) {
    $period = isset($_GET['yr']) ? filter_input(INPUT_GET, 'yr') : false;
    $mo = [];
    for ($j=1; $j<=12; $j++) {
        $mo[$j] = [];
    }
    foreach ($report_data as $item) {
        $expdate = explode("-", $item['expdate']);
        if ($expdate[0] === $digits[2]) {
            $month_item = array(
                $item['method'],
                $item['cdname'],
                $item['expdate'],
                $item['expamt'],
                $item['payee'],
                $item['acctchgd'],
                $item['paid']
            );
            $indx = intval($expdate[1]);
            array_push($mo[$indx], $month_item);
        }
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>User Report</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Rolling 3-month budget tracker" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <link href="../styles/standards.css" type="text/css" rel="stylesheet" />
    <style type="text/css">
       #page { margin-left: 16px; }
       #back { margin-left: 10px; }
       table { border-collapse: collapse; background-color: snow;
               border-width: 2px; border-style: outset; border-color: black; }
       th { text-align: left; padding: 4px 6px 4px 6px;
            background-color: #afcfaf; border-bottom: 2px 
            solid black; border-top: 2px solid black; 
            position: sticky; top: 0}
        tr.even { background-color: #dfecdf; }
       td { padding: 4px 6px 4px 6px; }
       .red  { color: firebrick; }
    </style>
</head>

<body>
<div id="page">
    <button id="back">Return to Budget</button>
    <?php
    if ($monthly) {
        include "formatMonth.php";
    } else {
        include "formatYear.php";
    }
    ?>
</div>

<script src="../scripts/jquery-1.12.1.js" type="text/javascript"></script>
<script type="text/javascript">
    $('#back').on('click', function() {
        var budpg = '../main/displayBudget.php';
        window.open(budpg, "_self");
    });

</script>

</body>
</html>
