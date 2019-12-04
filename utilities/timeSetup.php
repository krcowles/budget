<?php
/**
 * This file establishes month headers for the budget based on local timezone.
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
$thismo = intval($digits[0]) - 1; // array index is zero-based
$current_month = $month_names[$thismo];
switch ($thismo) {
case 1:
    $month_set = array(11, 12, 1);
    break;
case 2:
    $month_set = array(12, 1, 2);
    break;
default:
    $month_set = array($thismo-2, $thismo-1, $thismo);
}
// column headers
$month = [];
for ($i=0; $i<3; $i++) {
    $month[$i] = $month_names[$month_set[$i]];
}
$rollover = false;
if (!file_exists('LCM.txt')) {
    $handle = fopen("LCM.txt", "w");
    fputs($handle, $current_month);
    fclose($handle);
} else {
    $handle = fopen("LCM.txt", "r");
    $last = fgets($handle);
    if ($current_month !== $last) {
        $rollover = true;
    }
}
