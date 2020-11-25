<?php
/**
 * Thi
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
$username = filter_input(INPUT_POST, 'username');

$days = 365; // Number of days before cookie expires
$expire = time()+60*60*24*$days;
setcookie("epiz", $username, $expire, "/", "", false, true);
