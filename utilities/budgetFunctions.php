<?php
/**
 * This module contains any/all php functions utilized by the various routines
 * PHP Version 8.3.9
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
 * This function is invoked only on Non-Monthlies accounts;
 * ----- Test cases: -----
 *  I.   Bi-annual Payments
 *  II.  Annual Payments
 *  III. Other (Quarterly, Semi-annually)
 *  Note: other test cases represent very irregular scenarios and are
 *  not addressed in this routine.
 * ------------------------
 * 
 * @param string   $freq   how often a payment is to be made
 * @param string   $first  '1st' month of a cycle of payments as recorded in the db:
 *                         NOTE: this may not be the 1st in the calendar year
 * @param float    $amt    the amount applied at each instalment
 * @param string   $alt    'Odd' or 'Even' years for bi-annual payment
 * @param string   $paymo  the month the last payment was made
 * @param int      $payyr  year in which the last payment was made
 * @param string[] $months names of the months in a year
 * @param int      $thismo INDEX into $months representing current month
 * @param int      $thisyr 4-digit integer representing the current year
 * @param int      $record Unique record no. in `Irreg` table
 * 
 * @return array   [$acct_paid, $payInYr, $nextDueMo, $record]
 */
function prepNonMonthly(
    $freq, $first, $amt, $alt, $paymo, $payyr, $months,
    $thismo, $thisyr, $record
) {
    /*
     * Note that the incoming arg '$thismo' is 0-based, and is numerically one less
     * than the standard current month digit (e.g. $thismo = 10 => November); All
     * numeric-month-based arrays in this script are also 0-based, as is the pointer,
     * '$int_paymo' (the numeric representation of the argument '$paymo').
     */
    // initial values:
    $int_paymo = 0;
    $expected = 0;
    $acct_paid = false;
    $payInYr = $thisyr;
    $dist_months = []; // all months in which a payment would be due
    $dist_months[0] = array_search($first, $months);
    $payfreq = getFrequency($freq); // how many months in a year are pay months
    $incr_months = intval(12/$payfreq);
    $paypermo = round($amt/$incr_months, 2);
    if ($record === 3) {
        $debug = true;
    }
    if ($payfreq > 1) {
        /**
         * Create the list of payment months in the cycle [$dist_months]
         * and put them in ascending order; only $dist_months[0] has been
         * specified so far...
         */
        for ($j=1; $j<$payfreq; $j++) {
            // adjustment for base 0: $j - 1
            if ($dist_months[$j-1] + $incr_months > 11) {
                $next_mo = ($incr_months - 1) - (11 - $dist_months[$j-1]);
            } else {
                $next_mo = $dist_months[$j-1] + $incr_months;
            }
            $dist_months[$j] = $next_mo;
        }
        sort($dist_months);
    }
    if (empty($paymo) || empty($payyr)) { // Test case: A, B1, B2
        /**
         * In this case, the expense has not registered a payment. Set 
         * $int_paymo = first due month, and set $payyr to an arbitrary value
         * so that it appears that a payment hasn't been made this year.
         */
        $int_paymo = $dist_months[0];
        $payyr = 1000;
    } else {
        $int_paymo = array_search($paymo, $months); // 0-based
    }

    /**
     * PROCESS:
     * 1. Determine whether or not the account has been paid for this period;
     * 2. For bi-annual accts, determine if payment is in current yr:
     *    $payInYr = $thisyr, or $payInYr = $thisyr + 1
     * 3. Find the next month a payment is due; 
     * 4. Calculate the resulting $expected funds.
     * RETURN: $acct_paid, $payInYr, $nextDueMo, $expected (funding so far)
     */
    if ($payfreq === 1 || $payfreq === 0.5) {
        // Bi-annual or annual payments [there is only one month in $dist_months]
        $nextDueMo = $dist_months[0];
        if (!empty($alt)) { // Assumes SA_Yr is not empty for bi-annual payments!!
            // TEST CASE SET I: bi-annual payment; $payfreq = 0.5 [$payments = 24]
            if ($alt === 'Odd' && $thisyr%2 === 1) {
                if ($payyr === $thisyr) {  // Test case: D, E, G 
                    $payInYr = $thisyr + 2;
                    $acct_paid = true;
                    if ($thismo > $dist_months[0]) { // Test case: G
                        // begin accumulating
                        $delta = $thismo - $dist_months[0];
                    } else { // Test case:  // Test case: D, E
                        $delta = 0;
                    }
                } else {  // Test case: A, B, C
                    // not paid yet
                    $acct_paid = false; // redundant but clarifying
                    if ($thismo < $dist_months[0]) { // Test case: A
                        /**
                         * Remaining months to accum from previous odd yr:
                         *    (11 - $dist_months[0]);
                         * Months accum in even yr: 12
                         * Months accum so far this (odd) year: $thismo + 1
                         */
                        $delta = (11 - $dist_months[0]) + 12 + $thismo + 1;
                    } elseif ($thismo === $dist_months[0]) { // Test case: B
                        $delta = 24;
                    } else { // Test case: C
                        $delta = 24 + ($thismo - $dist_months[0]);
                    } 
                }   
            } elseif ($alt === 'Odd' && $thisyr%2 === 0) { // Test case: F
                $payInYr = $thisyr + 1;
                $delta = (11 - $dist_months[0]) + $thismo + 1;
            }
            if ($alt === 'Even' && $thisyr%2 === 0) {
                if ($payyr === $thisyr) { // Test case: D, E, G
                    $acct_paid = true;
                    $payInYr = $thisyr + 2;
                    if ($thismo > $dist_months[0]) { // Test case: G
                        // begin accumulating
                        $delta = $thismo - $dist_months[0];
                    } else { // Test case: D, E
                        $delta = 0;
                    }
                } else { // Test case: A, B, C
                    // not paid yet
                    $acct_paid = false; // redundant but clarifying
                    if ($thismo < $dist_months[0]) { // Test case: A
                        /**
                         * Remaining months to accum in last odd yr:
                         *      (11 - $dist_months[0]);
                         * Months accum in even yr: 12
                         * Months accum so far this (odd) year: $thismo + 1
                         */
                        $delta = (11 - $dist_months[0]) + 12 + $thismo + 1;
                    } elseif ($thismo === $dist_months[0]) {
                        // Test case B
                        $delta = 24;
                    } else { // Test case: C
                        $delta =  24 + ($thismo - $dist_months[0]);
                    }
                }   
            } elseif ($alt === 'Even' && $thisyr%2 === 1) { // Test case: F
                $payInYr = $thisyr + 1;
                $delta = (11 - $dist_months[0]) + $thismo + 1;
            }
            $expected = round($paypermo * $delta, 2);
        } else {
            // TEST CASE SET II. annual payment: $payfreq = 1
            if ($payyr === $thisyr) {
                // paid for the year, regardless if before or after $dist_months[0]
                $acct_paid = true;
                $payInYr = $thisyr + 1; // wait for next year to pay again
                if ($thismo > $dist_months[0]) { // Test case: E
                    // begin accumulating for next year...
                    $delta = ($thismo - $dist_months[0]) * $paypermo;
                    $expected = round($delta, 2);
                } else { // Test case: D
                    $expected = 0;
                }
            } else { // Test case: A, B, C
                $acct_paid = false; // redundant, but clarifying
                if ($thismo < $dist_months[0]) { // Test case: A 
                    // get accum from last year, add accum from this year:
                    $delta = 11 - $dist_months[0]; // accum from last yr
                    $expected = ($delta + $thismo +1 ) * $paypermo;
                } elseif ($thismo === $dist_months[0]) { // Test case: B
                    $expected = $amt;
                } else {
                    if ($thismo > $dist_months[0]) { // Test case: C
                        // last paid in prev yr:
                        $delta = ($thismo - $dist_months[0]) * $paypermo + $amt;
                        $expected = round($delta, 2);
                    }
                }
            }
        }
    }
    if ($payfreq > 1) {
        // TEST CASE SET III.
        $payouts = count($dist_months);
        /**
         * Determine the status of the account and expected funding:
         * Scenarios when $payyr = $thisyr:
         *  a. $thismo is in first pay cycle of yr;
         *  b. $thismo is in last pay cycle of yr;
         *  c. $thismo is in-between pay cycles.
         */
        if ($payyr < $thisyr) { // Test case: C, D [paid in prev. yr, or never]
            $acct_paid = false; //redundant but clarifying
            $nextDueMo = $dist_months[0];
            if ($thismo === $dist_months[0]) {
                $expected = $amt;
            } else {
                $prev_yr_accum = 11 - $dist_months[$payouts-1];
                $delta  = ($prev_yr_accum + $thismo + 1) * $paypermo;
                $expected = round($delta, 2);
            }
        } else { // Some payment has been made this year...
            if ($thismo <= $dist_months[0]) { // Scenario a;
                // Test case: A, B
                // the payment made is first payment of year (regardless of $paymo)
                $nextDueMo = $dist_months[1];
                $acct_paid = true;
                $expected = 0;
            } elseif ($thismo >= $dist_months[$payouts-1]) {
                // Scenario b: last pay cycle of year
                // Test case: I, J, K
                if (!($int_paymo > $dist_months[$payouts-2])) {
                    $acct_paid = false;
                    $nextDueMo = $dist_months[$payouts-1];
                    if ($thismo === $dist_months[$payouts-1]) {
                        // Test case: I
                        $expected = $amt;
                    } else { // somehow payment was missed? No test case...
                        $delta = ($thismo - $dist_months[$payouts-2]) * $paypermo;
                        $expected = round($delta, 2);
                    }
                } else { // Test case: J, K
                    $nextDueMo = $dist_months[0];
                    $acct_paid = true;
                    $payInYr = $thisyr + 1;
                    if ($thismo === $dist_months[$payouts-1]) {
                        $expected = 0;
                    } else {
                        $delta = ($thismo - $dist_months[$payouts-1]) * $paypermo;
                        $expected = round($delta, 2);
                    }
                }
            } else { // Scenario c:
                    // in-between cycles: assume the last payment was already made
                    // prior to or on the previous due date
                for ($i=1; $i<$payouts; $i++) {
                    if ($thismo <= $dist_months[$i]
                        && $thismo > $dist_months[$i-1]
                    ) {
                        if (!($int_paymo > $dist_months[$i-1])) {
                            $nextDueMo = $dist_months[$i];
                            // Test case: E, F, H
                            if ($thismo === $dist_months[$i]) {
                                // Test case: F
                                $expected = $amt;
                            } else {
                                // Test case: E, H
                                $acct_paid = false;
                                $delta = ($thismo - $dist_months[$i-1]) * $paypermo;
                                $expected = round($delta, 2);
                            }
                        } else { // Test case: G [paid for in $dist_months[$i]]
                            if ($int_paymo === $dist_months[$i]) {
                                $nextDueMo = $dist_months[$i+1];
                                $acct_paid = true;
                                $expected = 0;
                            }
                        }
                    }
                }
            }
        }
    
    }
    return [$acct_paid, $payInYr, $nextDueMo, $expected, $record]; 
}
 
/**
 * There is a need to sort the array of arrays created when invoking the
 * above functions in a for loop. This function is the compare function for
 * the usort callback.
 * 
 * @param array $first  first element of array
 * @param array $second next element of array
 * 
 * @return int $comp_value
 */
function compareNextDue($first, $second)
{
    return strnatcmp(intval($first[2]), intval($second[2]));
}
