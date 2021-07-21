<?php
/**
 * This script will drop the specified archive table
 * PHP Version 7.1
 * 
 * @package Admin
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../database/global_boot.php";

$droptbl = filter_input(INPUT_GET, 'drop');

$table = 'Year' . $droptbl;
$dropReq = "DROP TABLE {$table};";
$drop = $pdo->query($dropReq);
