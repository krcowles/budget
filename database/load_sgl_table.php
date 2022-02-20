<?php
/**
 * Load a single table into the database.
 * PHP Version 7.4
 * 
 * @package Ktesa
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../database/global_boot.php";

$table = filter_input(INPUT_POST, 'table');
$dropper = $pdo->query("DROP TABLE IF EXISTS {$table}");

// load the indicated table's sql file
$dbFile = "../database/" . $table . ".sql";
$lines = file($dbFile);
if (!$lines) {
    throw new Exception(
        __FILE__ . " Line: " . __LINE__ . 
        " Failed to read database from file: {$dbFile}."
    );
}
// load 'em
for ($i=0; $i<count($lines); $i++) {
    // Skip it if it's empty or a comment
    if (substr($lines[$i], 0, 2) == '--' || trim($lines[$i]) == '') {
        continue;
    }
    // There are 3 kinds of queries: CREATE TABLE, INSERT INTO, AND ALTER:
    // NOTE: Any present 'COMMIT' or C-style comments must be removed
    if (strpos($lines[$i], "CREATE TABLE") !== false) {
        $msg = '"' . $lines[$i] . '"';
        $create = "";
        do {
            $create .= $lines[$i];
        } while (strpos($lines[$i++], ";") === false);
        $pdo->exec($create);
        $i--;
    } elseif (strpos($lines[$i], "INSERT INTO") !== false) {
        $msg = '"' . $lines[$i] . '"';
        $insert = "";
        do {
            $insert .= $lines[$i];
        } while (strrpos($lines[$i], ";") !== strlen($lines[$i++])-2);
        $pdo->query($insert);
        $i--;
    } elseif (strpos($lines[$i], "ALTER") !== false) {
        $msg = '"' . $lines[$i] . '"';
        $alter = "";
        do {
            $alter .= $lines[$i];
        } while (strpos($lines[$i++], ";") === false);
        $pdo->query($alter);
        $i--;
    } else {
        throw new Exception(
            "Unrecognized table entry at db line " . $i . "<br />" . $lines[$i]
        );
    }
}
