<?php
/**
 * A simple script to list all the tables currently residing in the
 * connected database.
 * PHP Version 7.4
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.php>
 * @license No license to date
 */
session_start();
require "../database/global_boot.php";

$show = [];
$allTablesReq = "SHOW TABLES;";
$allTables = $pdo->query($allTablesReq)->fetchAll(PDO::FETCH_NUM);
foreach ($allTables as $table) {
    array_push($show, $table);
}
?>
<!DOCTYPE html>
<html lang="en-us">

<head>
    <title>Show Database Tables</title>
    <meta charset="utf-8" />
    <meta name="description" content="Create the USERS Table" />
    <meta name="author" content="Tom Sandberg and Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <style type='text/css'>
        body { 
            background-color: #eaeaea;
            margin-left: 24px; }
    </style>  
</head>

<body>

<h3>SHOW Database Tables</h3>

<div style="margin-left:16px;font-size:18px;">
    <p>Results from SHOW TABLES:</p>
    <ul>
    <?php for ($i=0; $i<count($show); $i++) : ?>
        <li><?=$show[$i][0];?></li>
    <?php endfor; ?>
    </ul>
    <p>DONE</p>
</div>

<script src="../scripts/jquery-1.12.1.js"></script>
<script src="../scripts/jquery-ui.js"></script>
<script src="../scripts/menus.js"></script>

</body>
</html>
