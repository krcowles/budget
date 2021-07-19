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
$db_files = scandir('../database');
$archives = [];
foreach ($db_files as $file) {
    $yrstrt = strpos($file, 'Year');
    if ($yrstrt !== false) {
        $yr = substr($file, $yrstrt+4, 4);
        array_push($archives, $yr);
    }
}
if (count($archives) > 0) {
    $js_archs = json_encode($archives);
} else {
    $js_archs = '';
}
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
    <title>Site Admin Tools</title>
    <meta charset="utf-8" />
    <meta name="description" content="Present tools for admin of site" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="../styles/bootstrap.min.css" type="text/css" rel="stylesheet" />
    <link href="../styles/admintools.css" type="text/css" rel="stylesheet" />
</head>
<body>

<div style="margin-left:24px;margin-top:16px;" id="tools">
    <fieldset class="bootshow">
        <legend class="bootshow">Database Management</legend>
        <p>Database Management Tools:</p>
        <button id="exall" type="button" class="btn btn-secondary">
            Export All Tables</button>
        &nbsp;&nbsp;[NOTE: Creates .sql file]<br />
        <button id="reload" type="button" class="btn btn-danger">
            Reload Database</button>
        &nbsp;&nbsp;[Drops All Tables and Loads All Tables]<br />
        <button id="drall" type="button" class="btn btn-danger">
            Drop All Tables</button><br />
        <button id="ldall" type="button" class="btn btn-secondary">
            Load All Tables</button>
        &nbsp;&nbsp;[NOTE: Tables must not exist]<br />
        <button id="show" type="button" class="btn btn-secondary">
            Show All Tables</button><br />
        <!-- End of Show/Set div w/form -->
    </fieldset><br />
    <fieldset class="bootshow">
        <legend class="bootshow">Miscellaneous Tools</legend><br />
        <button id="arch" type="button" class="btn btn-secondary">
            Archive Data</button>
        &nbsp;&nbsp;[This will eliminate data in the `Charges` table]<br />
        <div id="achoice">
            <select id="ayr">
                <option value="x" selected>Select A Year To Archive</option>
                <option value="2019">Archive 2019</option>
                <option value="2020">Archive 2020</option>
            </select>&nbsp;&nbsp;
            <button id="mkarch" type="button" class="btn btn-warning">
                Archive It</button>
        </div>
        <button id="lo" type="button" class="btn btn-secondary">
            Log out admin</button><br />
        <button id="version" type="button" class="btn btn-secondary">
            Get Current PHP Version</button><br />
        <button id="phpinfo" type="button" class="btn btn-secondary">
            PHPInfo</button><br />
    </fieldset>
   
<script type="text/javascript">
    var archives = <?=$js_archs;?>;
</script>
<script src="https://unpkg.com/@popperjs/core@2.4/dist/umd/popper.min.js"></script>
<script src="../scripts/bootstrap.min.js"></script>
<script src="../scripts/jquery-1.12.1.js"></script>
<script src="../scripts/admintools.js"></script>

</body>
</html>
