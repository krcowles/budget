<?php
/**
 * This file is used by multiple scripts to get/set current filenames based
 * on current month.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
date_default_timezone_set('America/Denver');
$date = date("m/d/Y");
$digits = explode("/", $date);
// DEFINITIONS:
$file_root = "../data/bud" . $digits[2];
$budget_data = $file_root . "_data.csv";
$credit_data = $file_root . "_charges.csv";
$month_names = array('January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December');
$thismo = intval($digits[0]) -1; // array index is zero-based
switch ($thismo) {
case 1:
    $month_set = array(11, 12, 1);
    $get_past  = 2;
    break;
case 2:
    $month_set = array(12, 1, 2);
    $get_past  = 1;
    break;
default:
    $month_set = array($thismo-2, $thismo-1, $thismo);
    $get_past = 0;
}
// column headers
$month = [];
for ($i=0; $i<3; $i++) {
    $month[$i] = $month_names[$month_set[$i]];
}
