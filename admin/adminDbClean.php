<?php
/**
 * Eliminate undesired test data from database
 * PHP Version 7.3
 * 
 * @package Admin
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require "../database/global_boot.php";

// just do cleanup of Budgets for userid = ?
$uid = '18';
$tbl = "Budgets";
$pdo->query("DELETE FROM {$tbl} WHERE `userid`={$uid};");

?>
<!DOCTYPE html>
<html lang="en-us">
<head>
    <title>Site Admin Tools</title>
    <meta charset="utf-8" />
    <meta name="description" content="Eliminate Test Data" />
    <meta name="robots" content="nofollow" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="../styles/bootstrap.min.css" type="text/css" rel="stylesheet" />
    <link href="../styles/admintools.css" type="text/css" rel="stylesheet" />
    <style type="text/css">
        #main { margin-left: 24px;}
    </style>
</head>
<body>

<?php //require "../main/navbar.php"; ?>

<div id="main">
    <br />
    <h5>Select the table you wish to 'clean'</h5>
    <select id="tbl">
        <option value="Budgets">Budgets</option>
        <option value="Cards">Cards</option>
        <option value="Charges">Charges</option>
        <option value="Deposits">Deposits</option>
        <option value="Users">Users</option>
    </select>
</div>

<script src="https://unpkg.com/@popperjs/core@2.4/dist/umd/popper.min.js"></script>
<script src="../scripts/bootstrap.min.js"></script>
</body>
</html>