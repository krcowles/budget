<?php
/**
 * This module contains any/all php functions utilized by the various routines
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to data
 */
/**
 * This function takes an amount derived from the Budgets table and
 * formats it in the accounting style.
 * 
 * @param float  $amt  The amount to be formatted
 * @param string $type If budget, don't use decimals
 * 
 * @return string $prepped The input data formatted
 */
function dataPrep($amt, $type) 
{
    // account formatting
    $dsign = '<span>$</span><span>';
    $negdollar = '<span class="negative">$</span><span class="negative">';
    if ($type === "budget") {
        $prepped = number_format($amt, 0, '.', ',');
        $prepped = $dsign . $prepped . '</span>';
    } else {
        $prepped = number_format($amt, 2, '.', ',');
        if ($prepped < 0) {
            $prepped = $negdollar . $prepped . '</span>';
        } else {
            $prepped = $dsign . $prepped . '</span>';  
        }
    }
    return $prepped;
}
/**
 * This function will attempt to 'clean' up potential UTF-16 formatting from
 * excel spreadsheet data.
 * 
 * @param array $excelDat an array of strings representing retrieved .csv data
 * 
 * @return array the 'cleaned' array returned
 */
function cleanupExcel($excelDat)
{
    foreach ($excelDat as &$item) {
        $len = strlen($item);
        $item = filter_var($item, FILTER_SANITIZE_STRING);
        $item = trim($item);
        $item = utf8_decode($item);
        $item = str_replace("?", "", $item); // after decode <feff> converts to '??'
    }
    return $excelDat;
}
/**
 * This function returns the number of months between each payment in a 
 * Non-Monthlies account
 * 
 * @param number $frequency How often are payments scheduled?
 * 
 * @return number $payfreq
 */
function getFrequency($frequency)
{
    switch ($frequency) {
    case "Bi-Annually":
        $payfreq = 0.5;
        break;
    case "Annually":
        $payfreq = 1;
        break;
    case "Semi-Annually":
        $payfreq = 2;
        break;
    case "Quarterly" :
        $payfreq = 4;
        break;
    case "Bi-Monthly" :
        $payfreq = 6;
    }
    return $payfreq;
}
/**
 * The current balance of all Non-Monthlies accounts can change during use
 * when logged in - this function will retrieve the current balance when invoked
 * 
 * @param string $type Either 'funds' or 'expected'
 * @param PDO    $pdo  Database PDO
 * @param number $uid  User's id
 * 
 * @return number $balance The current balance of all Non-Monthlies accounts
 */
function getCurrentNMBal($type, $pdo, $uid)
{
    $balanceReq = "SELECT SUM(`{$type}`) AS Total FROM `Irreg` WHERE `userid`=?;";
    $current = $pdo->prepare($balanceReq);
    $current->execute([$uid]);
    $sum = $current->fetch(PDO::FETCH_NUM); 
    $balance = floatval($sum[0]);
    return $balance;
}
/**
 * This function will:
 *  1. Calculate the next month a payment is due for a non-monthly account;
 *  2. Set the 'expected' balance for the current month. The expected balance
 *     is based on the expired time since the last paymnt.
 * 
 * @param string   $freq   how often a payment is to be made
 * @param string   $first  the 'first' month of a cycle of payment due months
 * @param float    $amt    the amount applied at each instalment
 * @param string   $alt    'Odd' or 'Even' years for bi-annual payment
 * @param string   $paymo  the month the last payment was made
 * @param int      $payyr  year in which payment was made
 * @param string[] $months names of the months in a year
 * @param int      $thismo INDEX into $months representing current month
 * @param int      $thisyr 4-digit integer representing the current year
 * 
 * @return array   [$wait, $next_due]
 */
function prepNonMonthly(
    $freq, $first, $amt, $alt, $paymo, $payyr, $months, $thismo, $thisyr
) {
    $expected = 0;
    $acct_paid = false;
    $wait = false; // true => already paid, OR it's an off year to pay [for autopays]
    $dist_months = []; // all months in which a payment would be due
    $dist_months[0] = array_search($first, $months);
    if ($paymo === '') {
        /**
         * In this case, the expense has not registered a payment, thus $payyr
         * will also be empty. Set $last_pd = $first and set $payyr to an arbitrary
         * value so that it appears that the payment hasn't been made this year.
         * The next statement works for annual/bi-annual, but when $payfreq > 1,
         * $first may not be the earliest month. This case will be addressed in
         * the case where $payfrq > 1
         */
        $last_pd = array_search($first, $months);
        $payyr = 1000;
    } else {
        $last_pd = array_search($paymo, $months);
    }
    $payfreq = getFrequency($freq); // how many months in a year are pay months
    $incr_months = intval(12/$payfreq);
    $paypermo = round($amt/$incr_months, 2);
 
    /**
     * Find the next month a payment is due; Calculate the resulting $expected funds
     * Note that the incoming $thismo is an index into $months, and is numerically
     * one less than the current month digit (e.g. $thismo = 10 => November);
     */
    if ($payfreq === 1 || $payfreq === 0.5) {
        // Bi-annual or annual payments
        $next_due = $dist_months[0];
        if (!empty($alt)) {
            // bi-annual payment; $payfreq = 0.5
            if ($alt === 'Odd' && $thisyr%2 === 1) {
                // this is the year to pay
                if ($thismo >= $last_pd && $payyr === $thisyr) {
                    $acct_paid = true;
                    $wait = true; // right year for payment, but already paid
                    $delta = $thismo - $last_pd;  // may = 0
                } elseif ($payyr === 1000
                    || ($thismo >= $last_pd && $payyr !== $thisyr)
                    || ($thismo < $last_pd )
                ) { 
                    // not paid yet: last years accum = (11 - $last_pd)
                    $delta =  (11 - $last_pd) + $thismo;
                }   
            } elseif ($alt === 'Odd' && $thisyr%2 === 0) {
                $wait = true; // Not this year!
                $delta = (11 - $last_pd) + $thismo;
            }
            if ($alt === 'Even' && $thisyr%2 === 0) {
                if ($thismo >= $last_pd && $payyr === $thisyr) {
                    $acct_paid = true;
                    $wait = true; // right year for payment, but already paid
                    $delta = $thismo - $last_pd;
                } elseif ($payyr === 1000
                    || ($thismo >= $last_pd && $payyr !== $thisyr)
                    || ($thismo < $last_pd)
                ) {
                    // not paid yet: last years accum = (11 - $last_pd)
                    $delta = (11 - $last_pd) + $thismo;
                }
            } elseif ($alt === 'Even' && $thisyr%2 === 1) {
                $wait = true; // Not this year!
                $delta = (11 - $last_pd) + $thismo;
            }
            $expected = round($paypermo * $delta, 2);
        } else {
            // annual payment: $payfreq = 1
            if ($thismo >= $last_pd && $payyr === $thisyr) {
                $acct_paid = true;
                $wait = true;
                $delta = ($thismo - $last_pd) * $paypermo;
                $expected = round($delta, 2);
            } else {
                // not paid yet
                if ($thismo === $last_pd) { // $payyr is nor $thisyr
                    $expected = $amt;
                } elseif ($thismo > $last_pd ) {
                    // here, $paymo was empty; assume new pay cycle
                    $wait = true;
                    $delta = ($thismo - $last_pd) * $paypermo;
                    $expected = round($delta, 2);
                } else {  // $thismo < $last_pd
                    $delta = (11 - $last_pd) + $thismo;
                    $expected = round($delta * $paypermo, 2);
                }
            }
        }
    }
    if ($payfreq > 1) {
        // get a list of payment months in the cycle [$dist_months]
        for ($j=1; $j<$payfreq; $j++) {
            if ($dist_months[$j-1] + $incr_months > 11) {
                // adjust for base 0: $pay_incr -1
                $next_mo = ($incr_months - 1) - (11 - $dist_months[$j-1]);
            } else {
                $next_mo = $dist_months[$j-1] + $incr_months;
            }
            $dist_months[$j] = $next_mo;
        }
        sort($dist_months);
        // find next payment due month
        $payouts = count($dist_months);
        for ($k=0; $k<$payouts; $k++) {
            // NOTE: $dist_months are in increasing order
            if ($thismo <= $dist_months[$k]) {
                // this is the next payment month
                $next_due = $dist_months[$k];
                if ($thismo === $dist_months[$k]) {
                    if ($k === 0) {
                        // this is the 1st pay month: get accum from last year
                        $delta = $thismo + (11 - $dist_months[$payouts -1]);
                    } else {
                        if ($payyr !== $thisyr) {
                            // not yet paid [or $paymo is empty]
                            $delta = $thismo - $dist_months[$k-1];
                        } else {
                            $delta = $incr_months;
                            $acct_paid = true;
                        }
                    }
                    $expected = round($delta * $paypermo, 2);
                } else {
                    // not there yet
                    if ($k === 0) {
                        // any from last dist_month of prev year
                        $delta = (11 - $dist_months[$payouts-1]) + $thismo;
                    } else {
                        $delta = $thismo - $dist_months[$k-1];  
                    }
                    $expected = round($delta * $paypermo, 2);
                }
                // got data, break loop
                break;
            } else {
                if ($k === $payouts - 1) {
                    // this is the 'last chance'
                    $next_due = $dist_months[0];
                    $delta = $thismo - $dist_months[$k];
                    $expected = round($delta * $paypermo, 2);
                    if ($thismo >= array_search($paymo, $months)
                        && $payyr === $thisyr
                    ) {
                        $acct_paid = true;
                    }
                }
            }
        }
    }
    return [$acct_paid, $wait, $next_due, $expected];  
}
