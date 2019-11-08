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
 * This function aids and abets the cleanup of Excel data and its translation into
 * utf-8 characters: otherwise data can get unepectedly garbled.
 * 
 * @param array $excelDat An array of string data (read or to be written)
 * 
 * @return array;
 */
/**
 * This function takes an item read from an Excel spreadsheet and assumes
 * it may have extraneous data, such as formatting codes for UTF-16
 * 
 * @param string $excelDat item read from Excel spreadsheet
 * 
 * @return string
 */
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
            