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
function cleanupExcel($excelDat)
{
    //array_map("utf8_encode", $excelDat); // Excel is not strictly UTF-8
    foreach ($excelDat as &$item) {
        $len = strlen($item);
        $item = filter_var($item, FILTER_SANITIZE_STRING);
        $item = trim($item);
        $item = utf8_decode($item);
        $item = str_replace("?", "", $item); // after decode <feff> converts to '??'
    }
    return $excelDat;
}
            