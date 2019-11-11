<?php
/**
 * This is an initial-only VERY INSECURE login script - to be used
 * only at alpha to verify budget tracking functionality and to obtain
 * user feedback.
 * PHP Version 7.1
 * 
 * @package BUDGET
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
$logins = array('krc'=> 'capnkrc', 'karen' => 'jimmy');

$user = filter_input(INPUT_POST, 'user');
$passwd = filter_input(INPUT_POST, 'passwd');

foreach ($logins as $key => $value) {
    if ($key = $user && $value === $passwd) {
        $loc = "../main/budget.php?dir=" . $user;
        header("Location: {$loc}");
    }
}