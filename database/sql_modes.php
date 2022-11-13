<?php
/**
 * This module simply established the modes used for MySQL via the pdo.
 * PHP Version 7.1
 *
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
$mode_str = 'SET sql_mode = "';
$file = $documentRoot . "database/sql_modes.ini";
$modes = file($file, FILE_IGNORE_NEW_LINES);
foreach ($modes as $setting) {
    if (substr($setting, 0, 1) == 'Y') {
        $mode_str .= substr($setting, 2, strlen($setting)-2) . ",";
    }
}
$mode_str = substr($mode_str, 0, strlen($mode_str)-1) . '"';
