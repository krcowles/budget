<?php
/**
 * The loader.php script performs the actual uploading of the database.sql file.
 * PHP Version 7.4
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require '../database/global_boot.php';
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
    <title>Load All Tables</title>
    <meta charset="utf-8" />
    <meta name="description" content="Present tools for admin of site" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="../styles/bootstrap.min.css" rel="stylesheet" />
    <link href="../styles/admintools.css" type="text/css" rel="stylesheet" />
    <style type="text/css">
        #progress { width: 420px; height: 36px; background-color: #ace600; }
        #bar { width: 0px; height: 36px; background-color: #aa0033; }
    </style>
    <script src="../scripts/jquery.js"></script>
<body>
<script src="../scripts/popper.min.js"></script>
<script src="../scripts/bootstrap.min.js"></script>  
<?php require "../main/navbar.php"; ?>
<p id="trail">Loading Database</p>
<p id="active" style="display:none">Admin</p>

<div style="margin-left:16px;">
<p>Please wait until the 'DONE' message appears below</p>
<div id="progress">
    <div id="bar"></div>
</div>
<p id="done" style="display:none;color:brown;">DONE: Tables imported successfully</p>
<script src="load_progress.js"></script>
<?php require "loader.php"; ?>
</div>

</body>
</html>
