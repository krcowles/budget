<?php
/**
 * This module is to be included wherever a script requires the budget data
 * in order to complete its functions. Using Excel CSV files results in UTF-16
 * encoding and byte ordering, hence budgetFunctions 'cleanupExcel' is required.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require_once "budgetFunctions.php";
require_once "timeSetup.php";

$status = "OK";
// arrays to be utilized by caller
$account_names = [];
$budgets = [];
$prev0 = [];
$prev1 = [];
$current = [];
$autopay = [];
$day = [];
$paid = [];   // not displayed
$income = []; // not displayed
// file checking:
if ($get_past && !file_exists($prev_bud)) { 
    $status = "E1: No budget data exists for previous year";
    // Don't go further
} else {
    // if $get_past (file does exits), data will be retrieved and updated below
    if (!$get_past) {
        if (!file_exists($budget_data)) {
            $status = "E2: No budget data has been created";
        } else {
            $handle = fopen($budget_data, "r");
            if ($handle === false) {
                $status = "E3: Budget data file could not be opened";
            } else {
                $headers = fgetcsv($handle);
                $headers = cleanupExcel($headers);
            }
        } 
    }
}
// proceed if OK
if ($status=== "OK") {
    $rollyear = false; // starting assumption: rollyear forces use of previous yr.
    $rollover = false; // starting assumption: rollover forces write of data
    if ($get_past) { // create new budget from previous year
        include "getPreviousBudget.php";
        if ($prev_status !== "OK") {
            $status = $prev_status; // Msgs E4, E5
        } else {
            $headers = array("Account Name", "Budget", "November", "December",
                "January", "Autopay", "Day", "Paid");
            for ($j=0; $j<count($prev_names); $j++) {
                $account_names[$j] = $prev_names[$j];
                $budgets[$j] = $prev_budgets[$j];
                $prev0[$j] = $prev_prevmonth[$j];
                $prev1[$j] = $prev_current[$j];
                $current[$j] = $prev_current[$j];
                $autopay[$j] = $prev_autopay[$j];
                $day[$j] = $prev_day[$j];
                $paid[$j] = $prev_paid[$j];
                $income[$j] = $prev_income[$j];
            }
            $rollyear = true;
        }
    } else { // retrieve current year budget
        // use $headers to determine whether or not a month's rollover
        $curr_mo_lbl = trim($headers[4]);
        if ($curr_mo_lbl !== $month[2]) {
            // rollover (month or year) - update $headers
            $headers[2] = $month[0];
            $headers[3] = $month[1];
            $headers[4] = $month[2];
            $rollover = true;
        }
        $recno = 0;
        while (($record = fgetcsv($handle)) !== false) {
            $record = cleanupExcel($record);
            if (trim($record[0]) === 'Temporary Accounts') {
                $account_names[$recno] = 'Temporary Accounts';
                $budgets[$recno] = 0;
                $prev0[$recno] = 0;
                $prev1[$recno] = 0;
                $current[$recno] = 0;
                $autopay[$recno] = '';
                $day[$recno] = '';
                $paid[$recno] = '';
                $income[$recno] = 0;
                $record = fgetcsv($handle);
                $record = cleanupExcel($record);
                $recno++;
            }
            $account_names[$recno] = $record[0];
            $budgets[$recno] = $record[1];
            if ($rollover) {
                $prev0[$recno] = $record[3];
                $prev1[$recno] = $record[4];
            } else {
                $prev0[$recno] = $record[2];
                $prev1[$recno] = $record[3];    
            }
            $current[$recno] = $record[4];
            if (count($record) < 9) {
                $autopay[$recno] = '';
                $day[$recno] = '';
                $paid[$recno] = '';
                $income[$recno] = 0;
            } else {
                $autopay[$recno] = $record[5];
                if (!empty($autopay[$recno])) {
                    $day[$recno] = intval($record[6]);
                    if ($rollover) {
                        $paid[$recno] = "N";
                    } else { 
                        $paid[$recno] = $record[7];
                    }
                } else {
                    $day[$recno] = '';
                    $paid[$recno] = '';
                }  
            }
            $income[$recno] = $record[8];
            $recno++;
        }
        fclose($handle);
    }
} // END OF STATUS OK
// If this is a rollover, then the new budget data needs to be written out
if ($rollover || $rollyear) {
    $handle = fopen($budget_data, "w");
    fputcsv($handle, $headers);
    for ($i=0; $i<count($account_names); $i++) {
        $output = array($account_names[$i], $budgets[$i], $prev0[$i], $prev1[$i],
            $current[$i], $autopay[$io], $day[$i], $paid[$i], $income[$i]);
        fputcsv($handle, $output);
    }
    fclose($handle);
}
/** 
 * The module produces the following data:
 * [Note that if a rollover month or year, new data will be written to $budget_data]
 *  $status         'OK', if budget data file was successfully opened
 *  $headers        the information appearing in the first line of the .csv data file
 *  $rollover       T/F - whether or not a monthly rollover has occurred
 *  $rollyear       T/F - whether or not an annual rollover has occurred
 *  $account_names  array holding all account names:
 *                  NOTE: an 'empty' line will exist for 'Temporary Accounts'
 *  $budgets        array holding each monthly budget amount
 *  $prev0          array holding balances from 2nd previous month
 *  $prev1          array holding balances from previous month  
 *  $current        array holding balancesfor current month  
 *  $autopay        array holding Cr/Dr against which an autopayment will be made
 *  $day            array holding correspondings days in the month the AP is due
 *  $paid           array holding paid/not paid status for each autopay
 *  $income         array holding distributed income to the account
 */
