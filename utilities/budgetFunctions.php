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
            