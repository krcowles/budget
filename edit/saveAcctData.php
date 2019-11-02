<?php
/**
 * This script saves all entries on the new budget into the $budget_data file
 * and then returns to the entry form. No auto pay data is entered for new
 * budgets - that will come later.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../utilities/timeSetup.php";

// Always present data for new accounts:
$headers = array("Account Name", "Budget", $month[0], $month[1], $month[2],
        "AutoPay", "Day");
$undis = array("Undistributed Funds", 0, 0, 0, 0, "", "");
$temp_accounts    = [];
$temp_accounts[0] = array("Temporary Accounts", "", "", "", "", "", "");
$temp_accounts[1] = array("Tmp1", 0, 0, 0, 0, "", "");
$temp_accounts[2] = array("Tmp2", 0, 0, 0, 0, "", "");
$temp_accounts[3] = array("Tmp3", 0, 0, 0, 0, "", "");
$temp_accounts[4] = array("Tmp4", 0, 0, 0, 0, "", "");
$temp_accounts[5] = array("Tmp5", 0, 0, 0, 0, "", "");
$accounts = []; // data to be written
array_push($accounts, $headers);
$csventry = []; // holds a line of data (array) to be pushed onto $accounts
// retrieve form data
$new_accounts = $_POST['acctname'];
$new_budgets  = $_POST['bud'];
$new_balances = $_POST['bal'];
if (isset($_POST['svdname'])) {
    $chg_accounts = $_POST['svdname'];
    $chg_budgets  = $_POST['svdbud'];
    $chg_balances = $_POST['svdbal'];
    $deletions    = isset($_POST['remove']) ? $_POST['remove'] : false;
} else {
    $chg_accounts = false;
}
// if there is previously saved data, update it and add it to $accounts
if ($chg_accounts) {
    $delcount = 0;
    for ($j=0; $j<count($chg_accounts); $j++) {
        $delete = false;
        if ($deletions) {
            if ($delcount < count($deletions)) {
                if ($deletions[$delcount] == $j) {
                    $delete = true;
                    $delcount++;
                }
            }
        }
        if ($chg_accounts[$j] == 'Undistributed Funds') {
            $delete = true;
        }
        if (!$delete) {
            $csventry[0] = filter_var($chg_accounts[$j]);
            $csventry[1] = filter_var($chg_budgets[$j]);
            $csventry[2] = 0;
            $csventry[3] = 0;
            $csventry[4] = filter_var($chg_balances[$j]);
            $csventry[5] = "";
            $csventry[6] = "";
            array_push($accounts, $csventry);
            $csventry = [];
        }
    }
}
// retrieve all new entries
for ($k=0; $k<count($new_accounts); $k++) {
    if (!empty($new_accounts[$k])) {
        $csventry[0] = filter_var($new_accounts[$k]);
        $csventry[1] = filter_var($new_budgets[$k]);
        $csventry[2] = 0;
        $csventry[3] = 0;
        $csventry[4] = filter_var($new_balances[$k]);
        $csventry[5] = "";
        $csventry[6] = "";
        array_push($accounts, $csventry);
        $csventry = [];
    }
}
// lastly, add the Undistributed Funds account
array_push($accounts, $undis);
// write it out
$acctdata = fopen($budget_data, "w");
for ($n=0; $n<count($accounts); $n++) {
    fputcsv($acctdata, $accounts[$n]);
}
for ($p=0; $p<6; $p++) {
    fputcsv($acctdata, $temp_accounts[$p]);
}
fclose($acctdata);
$newbud = "newBudget.php";
header("Location: {$newbud}");
