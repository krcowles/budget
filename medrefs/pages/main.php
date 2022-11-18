<?php
/**
 * The main display page for users to review and assign references.
 * PHP Version 7.2
 * 
 * @package MedRefs
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require_once "../../database/global_boot.php";
$new = isset($_GET['new']) ? true : false;
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
    <title>Medical References</title>
    <meta charset="utf-8" />
    <meta name="description"
        content="User-created list of medical cross-references" />
    <meta name="author" content="Developer Name" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="../styles/bootstrap.min.css" rel="stylesheet" />
    <link href="../styles/navbar.css" rel="stylesheet" />
    <link href="../styles/user_form.css" rel="stylesheet" />
    <link href="../styles/main.css" rel="stylesheet" />
    <script src="../scripts/jquery.min.js"></script>
    <style type="text/css">.noshow {display: none;}</style>
</head>
<body>
<!-- body tag must be read prior to invoking bootstrap.js -->
<script src="https://unpkg.com/@popperjs/core@2.4/dist/umd/popper.min.js"></script>
<script src="../scripts/bootstrap.min.js"></script>
<?php
require "../pages/navbar.php";
require "../pages/menu_modals.php";
?>

<p id="page_type" class="noshow">main</p>

<div id="editor">
    <form id="save_edits" method="post" action="save_page.php">
        <input id="ino" type="hidden" name="item_no" value="" />
        <input id="nme" type="hidden" name="item_nme" value="" />
    </form>
</div>

<div id="content">
    <table id="active_display">
</table>
</div>

</body>
</html>
