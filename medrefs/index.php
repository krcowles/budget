<?php
/**
 * This is the entry page for users. If the user aalready
 * has UserPages in the DB, the js will bypass this page and go to
 * the main.php page.
 * PHP Version 7.4
 * 
 * @package MedRefs
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require "../database/global_boot.php";

$checkUserReq = "SELECT @ FROM `Settings` WHERE `userid`=?;";
$checkUser = $mdo->prepare($checkUserReq);
$checkUser->execute([$_SESSION['userid']]);
$userState = $checkUser->fetch(PDO::FETCH_ASSOC);
$status = $userState ? 'ok' : 'no';
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
    <link href="styles/bootstrap.min.css" rel="stylesheet" />
    <link href="index.css" rel="stylesheet" />
</head>

<body>

<script src="https://unpkg.com/@popperjs/core@2.4/dist/umd/popper.min.js"></script>
<script src="scripts/bootstrap.min.js"></script>

<div class="noshow">
    <p id="status"><?=$status;?></p>
</div>

<div id="bg">
</div>

<div id="lead-in">
        <span id="lead">Create your personal list of medical references:</span>
        <ul id="medlist">
            <li>Doctors</li>
            <li>Phone numbers</li>
            <li>Medications</li>
            <li>Emergency contacts</li>
            <li>Anything important to you!</li>
        </ul>
        <p><a id="begin" href="pages/main.php" target="_self">
            Begin</a>
        </p>
</div>
<!-- Site First Entry modal -->
<div id="startup" class="modal" tabindex="-1"
    aria-labelled-by="Begin Creating" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Begin Creating</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                This section allows a user to create their own lists, and
                to define their own menu items, such as "My Doctors", "Medications",
                etc. The main page will display any list for the current menu
                item that is selected, if it exists, or go to a page where the list
                can be created. 
            </div>
            <div class="modal-footer">
                <button id="newlist" type="button"
                    class="btn btn-secondary">Proceed to List Creation</button>
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="scripts/jquery.min.js"></script>
<script src="index.js"></script>

</body>
</html>
