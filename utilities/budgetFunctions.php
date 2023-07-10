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
 * This function will determine expected balances for all
 * Non-Monthlies accounts based on payment frequency and
 * payment amount. Expected balance will vary each month
 * and also when an item is paid in the month due.
 * 
 * @param PDO      $pdo         Database pdo
 * @param number   $uid         User id
 * @param string[] $month_names From timeSetup.php
 * @param number   $thismo      Current month as 0-based index
 * @param number   $thisyear    Current 4-digit year
 * 
 * @return null
 */
function setNMExpected($pdo, $uid, $month_names, $thismo, $thisyear)
{
    $getNMDataReq = "SELECT * FROM `Irreg` WHERE `userid`=?;";
    $getNMData = $pdo->prepare($getNMDataReq);
    $getNMData->execute([$uid]);
    $nmdata = $getNMData->fetchAll(PDO::FETCH_ASSOC);

    for ($k=0; $k<count($nmdata); $k++) {
        // Enumerate months where funds are paid throughout the year
        $first_mo = $nmdata[$k]['first']; // <string> month name
        $index_mo = array_search($first_mo, $month_names);
        $dist_months = [];  // digits representing month_names indices
        $dist_months[0] = $index_mo;
        $payfreq = getFrequency($nmdata[$k]['freq']);
        $incr_months  = intval(12/$payfreq);
        $eoyr = false;
        if (!empty($nmdata[$k]['mo_pd'])) {
            $month_paid = array_search($nmdata[$k]['mo_pd'], $month_names);
            $acct_paid = $month_paid === $thismo ? true : false;
        } else {
            $acct_paid = false;
        } 
        /**
         * Annual payments are assigned $dist_months[0] and require
         * no further processing [$payfreq = 1]
         */
        if ($payfreq > 1) { // multiple distribution months per annum
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
        } elseif ($payfreq === 0.5) { // every other year...
            $eoyr = true;
            // odd or even years?
            if (!empty($items[$k]['SA_yr'])) {
                $dist_months[0] = $index_mo;
                if ($items[$k]['SA_yr'] === 'Odd' && $thisyear%2 === 1
                    && !empty($items[$k]['mo_pd'])
                ) {
                    $dist_months[0] = -1;
                } elseif ($items[$k]['SA_yr'] === 'Odd' && $thisyear%2 === 0) {
                    $dist_months[0] = -1;
                }
                if ($items[$k]['SA_yr'] === 'Even' && $thisyear%2 === 0
                    && !empty($items[$i]['mo_pd'])
                ) {
                    $dist_months[0] = -1;
                } elseif ($items[$k]['SA_yr'] === 'Even' && $thisyear%2 === 1) {
                    $dist_months[0] = -1;
                }
            }
            /*
            if ($nmdata[$k]['SA_yr'] === 'Odd' && $thisyear % 2 !== 0) {
                $dist_months[0] = $index_mo;
            } elseif ($nmdata[$k]['SA_yr'] === 'Even' && $thisyear %2 === 0) {
                $dist_months[0] = $index_mo;
            } else { // it's an 'off' year...
                $clearPdMoReq = "UPDATE `Irreg` SET `mo_pd`='' WHERE `record`=?;";
                $clearPdMo = $pdo->prepare($clearPdMoReq);
                $clearPdMo->execute([$nmdata[$k]['record']]);
                $dist_months[0] = -1;
            }
            */
        }
        // To calculate expected balance get # mos since last distribution
        if (count($dist_months) === 1) {
            // every-other-year?
            if ($eoyr && $dist_months[0] !== -1) {
                // pay/pd this year
                $calc_mos = $dist_months[0] >= $thismo ?
                    (11 - $dist_months[0]) + 12 + $thismo :
                    $thismo - $dist_months[0]; 
                if ($thismo === $dist_months[0] && $acct_paid ) {
                    $calc_mos = 0;
                }
            } elseif ($eoyr && $dist_months[0] === -1) {
                // pay next year
                $calc_mos = (11 - $dist_months[0]) + $thismo + 1;
            } else {
                // annual payment - once a yeaer
                $calc_mos = $thismo > $dist_months[0] ?
                    $thismo - $dist_months[0] : (11 - $dist_months[0]) + $thismo + 1;
                if ($thismo === $dist_months[0] && $acct_paid) {
                    $calc_mos = 0;
                }
            }
        } else { // multiple payments per year
            for ($n=1; $n<count($dist_months); $n++) {
                if ($thismo <= $dist_months[$n]
                    && $thismo > $dist_months[$n-1]
                ) {
                    $calc_mos = $thismo - $dist_months[$n-1];
                    if ($thismo === $dist_months[$n] && $acct_paid) {
                        $calc_mos = 0;
                    }
                    break;
                } elseif (($n === count($dist_months) - 1)
                    && $thismo > $dist_months[$n]
                ) {
                    $calc_mos = $thismo - $dist_months[$n];
                }
            }
        }
        // calculate expected balance
        $incr_payment = ceil($nmdata[$k]['amt']/$incr_months);
        $new_bal = $calc_mos * $incr_payment;
        // record new balance
        $udReq = "UPDATE `Irreg` SET `expected`=? WHERE `record`=?;";
        $updateIrreg = $pdo->prepare($udReq);
        $updateIrreg->execute([$new_bal, $nmdata[$k]['record']]);
    }
}