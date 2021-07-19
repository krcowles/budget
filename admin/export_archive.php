<?php
/** 
 * This script will export only the specified archive table automatically
 * and write it to the database directory
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../database/global_boot.php";
require "adminFunctions.php";
$archive = filter_input(INPUT_GET, 'archive');

// create array to export: (array in order to reuse exportDatabase function)
$tables = array($archive);

// mysqli prep:
$link =  mysqli_connect($HOSTNAME, $USERNAME, $PASSWORD, $DATABASE);
if (!$link) {
    throw new Exception(
        "Could not connect to the database using mysqli: File " .
        __FILE__ . "at line " . __LINE__
    );
}
if (!mysqli_set_charset($link, "utf8")) {
    throw new Exeption(
        "Function mysqli_set_charset failed when called from file " .
        __FILE__ . " line " . mysqli_error($link)
    );
}
// export function is contained in adminFunctions.php
exportDatabase($pdo, $link, $archive, $tables, 'A', $backup_name = false);
