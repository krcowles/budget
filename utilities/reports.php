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
require_once "../database/global_boot.php";
require_once "getCards.php";
require_once "timeSetup.php";

$id      = isset($_GET['id']) ? filter_input(INPUT_GET, 'id') : false;
$monthly = $id === 'morpt' ? true : false;
$annual  = $id === 'yrrpt' ? true : false;
$income  = $id === 'inc' ? true : false;

$datareq = "SELECT * FROM `Charges` WHERE `userid` = :uid;";
$data = $pdo->prepare($datareq); 
$data->execute(["uid" => $_SESSION['userid']]);
$report_data = $data->fetchALL(PDO::FETCH_ASSOC);
$rmethod = [];
$rcdname = [];
$rdate = [];
$ramt = [];
$rpayee = [];
$racct = [];
$rpaid = [];
if ($monthly) {
    $period = isset($_GET['mo']) ? filter_input(INPUT_GET, 'mo') : false;
    $mon = array_search($period, $month_names) + 1;
    foreach ($report_data as $item) {
        $expdate = explode("-", $item['expdate']);
        if ($expdate[0] === $digits[2] && $expdate[1] == $mon) {
            array_push($rmethod, $item['method']);
            array_push($rcdname, $item['cdname']);
            array_push($rdate, $item['expdate']);
            array_push($ramt, $item['expamt']);
            array_push($rpayee, $item['payee']);
            array_push($racct, $item['acctchgd']);
            array_push($rpaid, $item['paid']);
        }
    }
} 
if ($annual) {
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
<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Report</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Rolling 3-month budget tracker" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="../styles/bootstrap.min.css" type="text/css" rel="stylesheet" />
    <link href="../styles/reports.css" type="text/css" rel="stylesheet" />
</head>

<body>
<?php require "../main/navbar.php"; ?>
<div id="page">
    <?php
    if ($monthly) {
        include "formatMonth.php";
    } elseif ($annual) {
        include "formatYear.php";
    } elseif ($income) {
        include "formatIncome.php";
    }
    ?>
</div><br />
<?php require "../main/bootstrapModals.html"; ?>

<script src="https://unpkg.com/@popperjs/core@2.4/dist/umd/popper.min.js"></script>
<script src="../scripts/bootstrap.min.js"></script>
<script src="../scripts/jquery-1.12.1.js"></script>
<script src="../scripts/menus.js"></script>
<script src="../scripts/tableSort.js"></script>

</body>
</html>
