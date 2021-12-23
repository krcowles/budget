<?php
/**
 * This script will simply empty (drop) the database 'budget_data' in
 * preparation for loading the latest epizy database [epiz_24776673_BudgdetData.sql]
 * PHP Version 7.3
 *
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../database/global_boot.php";

$tables = array();
$data = $pdo->query("SHOW TABLES");
$tbl_list = $data->fetchALL(PDO::FETCH_NUM);
foreach ($tbl_list as $row) {
    array_push($tables, $row[0]);
}
// Execute the DROP TABLE command for each table:
foreach ($tables as $table) {
    echo "Dropping {$table}: ... ";
    $pdo->query("DROP TABLE {$table};");
    echo "Table Removed<br />";
}
echo "ALL TABLES DROPPED";
