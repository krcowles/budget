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
$last_yr = intval($digits[2] - 1);
$month_names = array('January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December');
$thismo = intval($digits[0]) -1; // array index is zero-based
$current_month = $month_names[$thismo];
switch ($thismo) {
case 1:
    $month_set = array(11, 12, 1);
    $get_past  = true;
    break;
case 2:
    $month_set = array(12, 1, 2);
    // past data should already have been written on rollover (case 1)
    $get_past  = false;
    break;
default:
    $month_set = array($thismo-2, $thismo-1, $thismo);
    $get_past = false;
}
// column headers
$month = [];
for ($i=0; $i<3; $i++) {
    $month[$i] = $month_names[$month_set[$i]];
}
