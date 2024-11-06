<?php
/**
 * This utility will generate a monthly or annual report, where the user
 * specifies the parameters. All expenses, paid or not, will be displayed.
 * PHP Version 8.3.9
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require_once "../database/global_boot.php";
require_once "getCards.php";
require_once "timeSetup.php";

$id       = isset($_GET['id']) ? filter_input(INPUT_GET, 'id') : false;
$monthly  = $id === 'morpt' ? true : false;
$annual   = $id === 'yrrpt' ? true : false;
$user_inc = $id === 'inc' ? true : false;
$xfrs     = $id === 'xfr' ? true : false;
if ($monthly || $annual) {
    $datareq = "SELECT * FROM `Charges` WHERE `userid` = :uid;";
    $data = $pdo->prepare($datareq); 
    $data->execute(["uid" => $_SESSION['userid']]);
    $report_data = $data->fetchALL(PDO::FETCH_ASSOC);
    if ($monthly) {
        $rmethod = [];
        $rcdname = [];
        $rdate = [];
        $ramt = [];
        $rpayee = [];
        $racct = [];
        $rpaid = [];
        $period = isset($_GET['mo']) ? filter_input(INPUT_GET, 'mo') : 'No Month';
        // NOTE: default is January, so 'mo' should always be specified...
        $mon = array_search($period, $month_names) + 1;
        $hdr1  = "Expenses for the month of {$period}";

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
    } else { // annual
        $period = isset($_GET['yr']) ? filter_input(INPUT_GET, 'yr') : false;
        // default is current year, so $period should never be false

        $hdr1 = "Expense Report for the Year {$period}";
        $mo = [];
        for ($j=1; $j<=12; $j++) {
            $mo[$j] = [];
        }
        foreach ($report_data as $item) {
            $expdate = explode("-", $item['expdate']);
            if ($expdate[0] === $period) {
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
} elseif ($user_inc) {
    $period = isset($_GET['incyr']) ? filter_input(INPUT_GET, 'incyr') : false;
    // default is current year, so $period should never be false
    $hdr1 = "Income Report for the Year {$period}";

    $depositReq = "SELECT * FROM `Deposits` WHERE `userid` = ? AND YEAR(`date`) = ?;";
    $depositQ = $pdo->prepare($depositReq);
    $depositQ->execute([$_SESSION['userid'], $period]);
    $deposits = $depositQ->fetchAll(PDO::FETCH_ASSOC);
} elseif ($xfrs) {
    $period = isset($_GET['xfryr']) ? filter_input(INPUT_GET, 'xfryr') : false;
    // default is current year so $period should never be false
    $hdr1 = "Transfers for the Year of {$period}";

    $getXfrsReq = "SELECT * FROM `Transfers` WHERE `userid`=?;";
    $getXfrs = $pdo->prepare($getXfrsReq);
    $getXfrs->execute([$_SESSION['userid']]);
    $userXfrs = $getXfrs->fetchAll(PDO::FETCH_ASSOC);
    // set reporting timeframe:
    $start = "{$period}-01-01";
    $end   = "{$period}-12-31";
    $yr_start = strtotime($start);
    $yr_end   = strtotime($end);
    $transfers = [];
    foreach ($userXfrs as $tran) {
        $xfd = strtotime($tran['xfr_date']);
        if ($xfd >= $yr_start && $xfd <= $yr_end) {
            $thistran = [];
            $thistran['from'] = $tran['from_acct'];
            $thistran['to']   = $tran['to_acct'];
            $thistran['amt']  = $tran['amount'];
            $thistran['date'] = $tran['xfr_date'];
            array_push($transfers, $thistran);
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
    <link href="../styles/modals.css" type="text/css" rel="stylesheet" />
    <link href="../styles/reports.css" type="text/css" rel="stylesheet" />
</head>

<body>
<?php
require "../main/navbar.php";
require "../main/bootstrapModals.php";
?>
<div id="page"></div><br />
<div id="tableOfResults" style="margin-left: 24px;">
    <?php if ($monthly) {
        include "formatMonth.php";
    } elseif ($annual) {
        include "formatYear.php";
    } elseif ($user_inc) {
        include "formatIncome.php";
    } else {
        include "formatTransfers.php";
    }
    ?>
</div>

<script src="https://unpkg.com/@popperjs/core@2.4/dist/umd/popper.min.js"></script>
<script src="../scripts/bootstrap.min.js"></script>
<script src="../scripts/jquery.min.js"></script>
<script src="../scripts/menus.js"></script>
<script src="../scripts/tableSort.js"></script>

</body>
</html>
