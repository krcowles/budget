<?php
/** 
 * This script will export all tables automatically and download
 * them to the client machine's browser. Refer to comments in the
 * adminFunctions.php module: the export utilizes both $pdo for
 * accessing the current db, and mysqli for formulating the .sql
 * file's string contents. 
 * PHP Version 7.1
 * 
 * @package MedRefs
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../../database/global_boot.php";
require "adminFunctions.php";

$download = filter_input(INPUT_GET, 'dwnld');
// create array of tables to export: NOTE: due to foreign keys, EHIKES must be first
$data = $mdo->query("SHOW TABLES;");
$tbls_list = $data->fetchALL(PDO::FETCH_BOTH);
$tables = [];
foreach ($tbls_list as $row) {
    array_push($tables, $row[0]);
}
$backup_name = "mybackup.sql";
// mysqli prep:
$link =  mysqli_connect($MHOST, $MUSER, $MPASS, $MDATA);
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
exportDatabase($mdo, $link, $MDATA, $tables, $download, $backup_name = false);
