<?php
/**
 * Display each budget account's current funding level via 
 * session variable evaluated in displayBudget.php
 * PHP Version 7.4
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license
 */
session_start();

$state = filter_input(INPUT_GET, 'disp');

$startup = file_get_contents("../database/userFunding.php");
$user_state = strpos($startup, "fundClass = '") + 13;
$class = substr($startup, $user_state, 6);
if ($class === 'noshow' && $state === 'on') {
    $new_state = str_replace(
        "fundClass = 'noshow'", "fundClass = 'unhide'", $startup
    );
} elseif ($class === 'unhide' && $state === 'off') {
    $new_state = str_replace(
        "fundClass = 'unhide'", "fundClass = 'noshow'", $startup
    );
}
file_put_contents("../database/userFunding.php", $new_state);
