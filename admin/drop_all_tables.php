<?php
/**
 * This module performs one of two actions based on the query string.
 * If the query string contains the variable "no", then all tables are
 * dropped and the program is exited. If the variable is not set, then
 * the module will first drop all tables and then reload them.
 * PHP Version 7.1
 * 
 * @package Admin
 * @author  Tom Sandberg and Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../database/global_boot.php";

$tables = array();
$data = $pdo->query("SHOW TABLES");
$tbl_list = $data->fetchALL(PDO::FETCH_NUM);
foreach ($tbl_list as $row) {
    array_push($tables, $row[0]);
}
$tblcnt = count($tables); // total number of database tables
if (isset($_REQUEST['no'])) {
    $action = 'Drop All Tables';
} else {
    $action = "Reload Database";
}
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
    <title><?= $action;?></title>
    <meta charset="utf-8" />
    <meta name="description"
        content="Drop (and Load if reqested) the specified Tables" />
    <meta name="author" content="Tom Sandberg and Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <link href="../styles/jquery-ui.css" type="text/css" rel="stylesheet" />
    <link href="../styles/ktesaPanel.css" type="text/css" rel="stylesheet" />
    <style type="text/css">
        body {
            background-color: #eaeaea;
            margin: 0px;}
        #progress { width: 420px; height: 36px; background-color: #ace600; }
        #bar { width: 0px; height: 36px; background-color: #aa0033; }
    </style>
    <script src="../scripts/jquery-1.12.1.js"></script>
    <script src="../scripts/jquery-ui.js"></script>
</head>
<body>
<p id="trail"><?= $action;?></p>

<div style="margin-left:16px;font-size:18px;">
<?php
// Execute the DROP TABLE command for each table:
for ($i=0; $i<$tblcnt; $i++) {
    echo "Dropping {$tables[$i]}: ... ";
    $pdo->query("DROP TABLE {$tables[$i]};");
    echo "Table Removed<br />";
}
?>
<?php if ($action == 'Reload Database') : ?>
    <div style="margin-left:16px;">
    <p>Please wait until the 'DONE' message appears below</p>
        <br />
    <div id="progress">
        <div id="bar"></div>
    </div>
    <p id="done" style="display:none;color:brown;">DONE: Tables imported
        successfully</p>
    <script src="load_progress.js"></script>
        <?php include 'loader.php'; ?>
    <p>DONE: Tables imported successfully</p>
    </div>
<?php endif; ?>
</div>

</body>
</html>
