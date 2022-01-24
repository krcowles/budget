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

// Locate any existing data archives
$db_files = scandir('../database');
$archives = [];
$exarchs = '';
foreach ($db_files as $file) {
    $yrstrt = strpos($file, 'Year');
    if ($yrstrt !== false) {
        $yr = intval(substr($file, $yrstrt+4, 4));
        array_push($archives, $yr);
        $exarchs .= '<option value="' . $file . '">' . $file . '</option>';
    }
}
if (count($archives) > 0) {
    $js_archs = json_encode($archives);
} else {
    $js_archs = '""';
}
if (empty($exarchs)) {
    $exarchs = '<option value="0">No archives saved</option>';
}
// Extablish archive selections
date_default_timezone_set('America/Denver');
$date = date("Y/m/d");
$digits = explode("/", $date);
$current_yr = intval($digits[0]);
$eligible = '';
$arch_yr = 2019;  // the earliest year data was available
while ($arch_yr < $current_yr) {
    if (!in_array($arch_yr, $archives)) {
        $eligible .= '<option value="' . $arch_yr . '">Archive  ' .
            $arch_yr . '</option>';
    }
    $arch_yr++;
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
        <button id="ld_sgl" type="button" class="btn btn-secondary">
            Load Single Table</button>
        &nbsp;&nbsp;Table to Load:&nbsp;&nbsp;
        <input id="tblname" type="text" size="16" /><br />
        <!-- End of Show/Set div w/form -->
    </fieldset><br />
    <fieldset class="bootshow">
        <legend class="bootshow">Miscellaneous Tools</legend><br />
        <button id="arch" type="button" class="btn btn-secondary">
            Archive Data</button>
        &nbsp;&nbsp;[This will eliminate data in the `Charges` table and create
            a separate archive file]<br />
        <span style="position:relative;top:6px;">Current archives:</span>
            &nbsp;&nbsp;<select id="archs"><?=$exarchs;?></select><br />
        <div id="achoice">
            <select id="ayr">
                <option value="x" selected>Select A Year To Archive</option>
                <?=$eligible;?>
            </select>&nbsp;&nbsp;
            <button id="mkarch" type="button" class="btn btn-warning">
                Archive It</button>
        </div>
        <button id="ldarch" type="button" class="btn btn-secondary">
            Load Archive</button><br />
        <div id="ldayr">
            <select id="ldyr">
                <option value="x">Select Year To Load</option>
            </select>&nbsp;&nbsp;
            <button id="larch" type="button" class="btn btn-warning">
                Load It</button>
        </div>
        <button id="lo" type="button" class="btn btn-secondary">
            Log out admin</button><br />
        <button id="version" type="button" class="btn btn-secondary">
            Get Current PHP Version</button><br />
        <button id="phpinfo" type="button" class="btn btn-secondary">
            PHPInfo</button><br />
    </fieldset>
    <fieldset class="bootshow">
        <legend>Debug Tools</legend>
        <span class="dshift">Userid: <input id="auid" type="text" size="4" />
        </span>&nbsp;&nbsp;
        <button id="newusr" type="button" class="btn btn-warning">
            Switch User</button>
        <br />
    </fieldset>
</div>
   
<script type="text/javascript">
    var archives = <?=$js_archs;?>;
</script>
<script src="https://unpkg.com/@popperjs/core@2.4/dist/umd/popper.min.js"></script>
<script src="../scripts/bootstrap.min.js"></script>
<script src="../scripts/jquery.min.js"></script>
<script src="../scripts/admintools.js"></script>

</body>
</html>
