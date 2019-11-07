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
$handle = fopen($budget_data, "r");
if ($handle === false) {
    $status = "No budget data found";
} else {
    // arrays to be utilized by caller
    $account_names = [];
    $budgets = [];
    $prev0 = [];
    $prev1 = [];
    $current = [];
    $autopay = [];
    $day = [];
    $paid = [];
    $headers = fgetcsv($handle);
    $headers = cleanupExcel($headers);
    if ($headers[0] === 'None') {
        $status = "No budget data entered";
    } else {
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
                $record = fgetcsv($handle);
                $recno++;
            }
            $account_names[$recno] = $record[0];
            $budgets[$recno] = $record[1];
            $prev0[$recno] = $record[2];
            $prev1[$recno] = $record[3];
            $current[$recno] = $record[4];
            if (count($record) < 8) {
                $autopay[$recno] = '';
                $day[$recno] = '';
                $paid[$recno] = '';
            } else {
                $autopay[$recno] = $record[5];
                if (!empty($autopay[$recno])) {
                    $day[$recno] = intval($record[6]);
                    $paid[$recno] = $record[7];
                } else {
                    $day[$recno] = '';
                    $paid[$recno] = '';
                }
            }
            $recno++;
        }
    }
}
fclose($handle);
/** 
 * The module produces the following data:
 *  $status         'OK', if budget data file was successfully opened
 *  $headers        the information appearing in the first line of the .csv data file
 *  $account_names  array holding all account names:
 *                  NOTE: an 'empty' line will exist for 'Temporary Accounts'
 *  $budgets        array holding each monthly budget amount
 *  $prev0          array holding balances from 2nd previous month
 *  $prev1          array holding balances from previous month  
 *  $current        array holding balancesfor current month  
 *  $autopay        array holding Cr/Dr against which an autopayment will be made
 *  $day            array holding correspondings days in the month the AP is due
 *  $paid           array holding paid/not paid status for each autopay
 */
