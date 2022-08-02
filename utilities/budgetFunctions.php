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
 * Retrieve the user's Non-monthly data items and calculate the expected
 * balance for the current month. The calculation assumes all monthly incomes
 * have been deposited, or, in essence, that it is the end of the current month.
 * 
 * @param PDO     $pdo         The database PDO class
 * @param array   $month_names The full names of all 12 months
 * @param integer $current_yr  The current 4-digit year
 * @param integer $current_mo  The current month (integer)
 * 
 * @return float $balance Expected balance
 */
function getExpectedBalance($pdo, $month_names, $current_yr, $current_mo)
{
    $relations = array( // no of payments in one year
        "Bi-Annually"   => 0.5,
        "Annually"      => 1,
        "Semi-Annually" => 2,
        "Quarterly"     => 4,
        "Bi-Monthly"    => 6
    );
    $freqs = array_keys($relations);
    // Assume 'first' mo is January, then adjust later
    $base_freq  = [[0], [0], [0, 6], [0, 3, 6, 9], [0, 2, 4, 6, 8, 10]];
    $cycle_time = [12, 12, 6, 3, 2];
    // Get user's data
    $comboReq = "SELECT * FROM `Irreg` WHERE `userid`=?;";
    $nonmonthlies = $pdo->prepare($comboReq);
    $nonmonthlies->execute([$_SESSION['userid']]);
    $items = $nonmonthlies->fetchALL(PDO::FETCH_ASSOC);
    $ebal = 0;  // expected balance as of this month
    $item_total = count($items);
    if ($item_total === 0) {
        return $ebal;
    } else {
        for ($j=0; $j<$item_total; $j++) {
            $freq   = $items[$j]['freq'];
            $amt    = $items[$j]['amt'];
            $first  = $items[$j]['first'];
            $altyrs = $items[$j]['SA_yr'];
            $fmonth = array_search($first, $month_names); // base 0 array
            if ($freq === 'Bi-Annually') {
                /**
                 * Bi-Annual payments are treated differently
                 */
                $paymo = $fmonth;
                $bud = $amt/24; // monthly budget amt, not rounded up
                $thisalt = $current_yr % 2 === 1 ? 'Odd' : 'Even';
                if ($altyrs === $thisalt) {
                    // payable this year
                    if ($current_mo < $paymo) {  // pay month not yet arrived
                        $postpay = (11 - $fmonth) * $bud;
                        $ebal += $postpay + (12 + $current_mo) * $bud;
                    } elseif ($current_mo > $paymo) {  // pay month passed
                        $ebal += ($current_mo - $paymo) * $bud;
                    } else {  // payable this month ... no action
                        $ebal += 0;
                    }
                } else {
                    /**
                     * Payable next year; therefore, collect whatever remains
                     * from last year after the payment and add bud amts for
                     * this year to date
                     */
                    $postpay = (11 - $fmonth);
                    $ebal += ($postpay + $current_mo) * $bud;   
                }
            } else {
                /**
                 * All other frequency types (NOT Bi-Annual)
                 */
                $mo_indx = array_search($freq, $freqs);
                // payment frequency (array of month numbers)
                $item_freq  = $base_freq[$mo_indx];
                $item_cycle = $cycle_time[$mo_indx];
                // month of first payment
                $item_start = $fmonth;
                // adjust $item_freq array per $item_start month
                if (count($item_freq) > 1) {
                    $adder = 0;
                    for ($k=0; $k<count($item_freq); $k++) {
                        if ($item_freq[$k] === $item_start) {
                            /**
                             * In this case (it is the month of payment),
                             * The balance may start off as fully funded
                             * but will eventually be paid off and the
                             * balance will then be 0. This routine assumes
                             * that the item will be paid off (by month's end).
                             */
                            $adder = 0;
                            break;
                        } else {
                            if ($item_start < $item_freq[$k]) {
                                $adder = $item_start - $item_freq[$k-1];
                                break;
                            } elseif ($k === count($item_freq) -1) {
                                $adder = $item_start - $item_freq[$k];
                            }
                        }
                    }
                    foreach ($item_freq as &$period) {
                        $period += $adder;
                    }
                } else {
                    $item_freq[0] = $item_start;
                }
                // calculate monthly budget amount
                $ann_bud = $relations[$freq] * $amt;
                $bud = $ann_bud/12; // monthly amt not rounded up
                // determine where today is with respect to adjusted period months
                for ($i=0; $i<count($item_freq); $i++) {
                    $freq_i = $item_freq[$i]; // simplify typing!
                    if ($freq_i === $current_mo) { 
                        $ebal += 0; // don't add (most likely be paid this month)
                        break;
                    } elseif ($current_mo < $freq_i) { // no payment yet
                        $months_till_pay = $freq_i - $current_mo; 
                        $delta = $item_cycle - $months_till_pay;
                        // what portion of the cycle is this?
                        $ratio = $delta/$item_cycle;
                        $ebal += $ratio * $amt;
                        break;
                    } elseif ($i === (count($item_freq) -1)) { // end of the array
                        $delta = $current_mo - $freq_i;
                        $ratio = $delta/$item_cycle;
                        $ebal += $ratio * $amt;
                    }
                }
            }
        }
        return ceil($ebal);
    }
}  
