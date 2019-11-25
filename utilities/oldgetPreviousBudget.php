<?php
/**
 * This module extracts budget data for the last two months of the old year's
 * budget; This module should only be invoked once, when the year rolls over.
 * After that the updated data should already be written into $budget_data.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require_once "budgetFunctions.php";
require_once "timeSetup.php";

$prev_status = "OK";
$prev = fopen($prev_bud, "r"); // getBUdgetData.php checked file existence
if ($prev === false) {
    $prev_status = "E5: Cannot open past year's budget data fille";
} else {
    // arrays to be utilized by caller
    $prev_names = [];
    $prev_budgets = [];
    $prev_prevmonth = [];
    $prev_current = [];
    $prev_autopay = [];
    $prev_day = [];
    $prev_paid = [];
    $prev_headers = fgetcsv($prev); // not needed, but primes the while loop
    $entry = 0;
    while (($prevdat = fgetcsv($prev)) !== false) {
        $prevdat = cleanupExcel($prevdat);
        if (trim($prevdat[0]) === 'Temporary Accounts') {
            $prev_names[$entry] = 'Temporary Accounts';
            $prev_budgets[$entry] = 0;
            $prev_prevmonth[$entry] = 0;
            $prev_current[$entry] = 0;
            $prev_autopay[$entry] = '';
            $prev_day[$entry] = '';
            $prev_paid[$entry] = '';
            $prevdat = fgetcsv($handle);
            $entry++;
        }
        $prev_names[$entry] = $prevdat[0];
        $prev_budgets[$entry] = $prevdat[1];
        $prev_prevmonth[$entry] = $prevdat[3];
        $prev_current[$entry] = $prevdat[4];
        if (count($prevdat) < 8) {
            $prev_autopay[$entry] = '';
            $prev_day[$entry] = '';
            $prev_paid[$entry] = '';
        } else {
            $prev_autopay[$entry] = $prevdat[5];
            if (!empty($prev_autopay[$entry])) {
                $prev_day[$entry] = intval($prevdat[6]);
                $prev_paid[$entry] = $prevdat[7];
            } else {
                $prev_day[$entry] = '';
                $prev_paid[$entry] = '';
            }
        }
        $entry++;
    }
}
fclose($prev);
/** 
 * This module produces the following data:
 *  $prev_status        The status from trying to retrieve previous year's data
 *  $prev_names         Prev. yr account names
 *  $prev_budgets       Prev. yr budget amounts
 *  $prev_prevmonth     Prev. yr Current-1 account balance
 *  $prev_current       Prev. yr Current account balance
 *  $prev_autopay       Prev. yr autopay account
 *  $prev_day           Prev. yr autopay day of the month
 *  $prev_paid          Prev. yr Paid status
 */
