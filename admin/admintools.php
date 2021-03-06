<?php
/**
 * Administration tools for the admin are included here. These
 * comprise buttons to carry out certain admin tasks, and are grouped
 * and ordered based on current usage. Note: first implementation has 
 * only the database backup (export all tables), and database reload.
 * PHP Version 7.1
 * 
 * @package Admin
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../database/global_boot.php";
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
    <title>Site Admin Tools</title>
    <meta charset="utf-8" />
    <meta name="description" content="Present tools for admin of site" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <link href="../styles/admintools.css" type="text/css" rel="stylesheet" />
</head>
<body>

<div style="margin-left:24px;margin-top:16px;" id="tools">
    <fieldset>
        <legend>Database Management</legend>
        <p>Database Management Tools:</p>
        <button id="exall">Export All Tables</button>
            [NOTE: Creates .sql file]<br />
        <button id="reload">Reload Database</button>&nbsp;
            [Drops All Tables and Loads All Tables]<br />
        <button id="drall">Drop All Tables</button><br />
        <button id="ldall">Load All Tables</button>
            [NOTE: Tables must not exist]<br />
        <button id="show">Show All Tables</button><br />
        <!-- End of Show/Set div w/form -->
    </fieldset><br />
    <fieldset>
        <legend>Miscellaneous Tools</legend>
        <button id="lo">Log out admin</button><br />
        <button id="version">Get Current PHP Version</button><br />
        <button id="phpinfo">PHPInfo</button><br />
    </fieldset>
    
<script src="../scripts/jquery-1.12.1.js"></script>
<script src="../scripts/admintools.js"></script>

</body>
</html>
