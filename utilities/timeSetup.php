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
$tbldate = $digits[2] . '-' . $digits[0] . '-' . $digits[1];
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
// days in month
$daysInMonth = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
$leapYr = intval($digits[2])%4 === 0 ? true : false;
if ($leapYr) {
    $daysInMonth[1] = 29;
}

// column headers
$month = [];
for ($i=0; $i<3; $i++) {
    $month[$i] = $month_names[$month_set[$i]];
}
$rollover = false;
$LCM = "SELECT `LCM` FROM `Users` WHERE `user` = :uid;";
$moupdte = $pdo->prepare($LCM);
$moupdte->execute(["uid" => $user]);
$mo = $moupdte->fetch(PDO::FETCH_ASSOC);
if (!empty($mo['LCM'])) {
    if ($mo['LCM'] !== $current_month) {
        $rollover = true;
    }
}
if (empty($mo['LCM' || $rollover])) {
    $newup = "UPDATE `Users` SET `LCM` = :mo WHERE `username` = :uid;";
    $setmo = $pdo->prepare($newup);
    $setmo->execute(["mo" => $current_month, "uid" => $user]);
}
