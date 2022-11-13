<?php
/**
 * This is either: the default blank form to be used for creating a page
 * associated with the user's list item in the navigation bar; or, the
 * existing page in edit mode.
 * PHP Version 7.4
 * 
 * @package MedRefs
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require "../../database/global_boot.php";

define("DEFAULT_NO_OF_ROWS", 4);
define("DEFAULT_NO_OF_COLS", 4);
$item_no  = filter_input(INPUT_GET, 'menu');
$selected = filter_input(INPUT_GET, 'item');
$saved    = isset($_GET['save']) ? filter_input(INPUT_GET, 'save') : 'n';
$defcol1 = <<<COL1
Reference or Name<br />
e.g. Dr.'s Name
COL1;
$defcol2 = <<<COL2
Description or Content<br />
e.g. Phone No.
COL2;
$defcol3 = <<<COL3
Notes or Qualifier<br />
e.g. Specialty
COL3;
$defcol4 = <<<COL4
<br />
Additional Label Here
COL4;

$defaultHdrs = array($defcol1, $defcol2, $defcol3, $defcol4);
$default = true;
$chkbox_status = 'uncheck';
if ($saved === 'n') {
    $box1 = array_fill(0, DEFAULT_NO_OF_ROWS, "");
    $box2 = array_fill(0, DEFAULT_NO_OF_ROWS, "");
    $box3 = array_fill(0, DEFAULT_NO_OF_ROWS, "");
    $box4 = array_fill(0, DEFAULT_NO_OF_ROWS, "");
    $rowcnt = count($box1);
    $user_hdrs = $defaultHdrs;
} else {
    $default = false;
    $add_chkbox = true;
    $newdatReq = "SELECT * FROM `UserPages` WHERE `userid`=? AND `menu_item`=?;";
    $newdat = $mdo->prepare($newdatReq);
    $newdat->execute([$_SESSION['userid'], $item_no]);
    $page_data = $newdat->fetch(PDO::FETCH_ASSOC);
    $box1str = $page_data['box1'];
    $box2str = $page_data['box2'];
    $box3str = $page_data['box3'];
    $box4str = $page_data['box4'];
    $box1 = explode("|", $box1str);
    $box2 = explode("|", $box2str);
    $box3 = explode("|", $box3str);
    $box4 = explode("|", $box4str);
    // add an extra blank row in case more data is desired for the list
    $rowcnt = count($box1) + 1;
    array_push($box1, "");
    array_push($box2, "");
    array_push($box3, "");
    array_push($box4, "");
    $headerStr = $page_data['headers'];
    if (!empty($headerStr)) {
        $user_hdrs = explode("|", $headerStr);
    } else {
        $user_hdrs = $defaultHdrs;
    }
    if (!empty($box4Str) || !empty($user_hdrs[3])) {
        $add_chkbox = false;
    }
    $chkbox_status = $add_chkbox ? 'uncheck' : 'check';
}
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
    <title>List Creation Form</title>
    <meta charset="utf-8" />
    <meta name="description"
        content="User-created list of medical cross-references" />
    <meta name="author" content="Developer Name" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="../styles/bootstrap.min.css" rel="stylesheet" />
    <link href="../styles/navbar.css" rel="stylesheet" />
    <link href="../styles/user_form.css" rel="stylesheet" />
    <script src="../scripts/jquery.min.js"></script>
</head>
<body>
<!-- body tag must be read prior to invoking bootstrap.js -->
<script src="https://unpkg.com/@popperjs/core@2.4/dist/umd/popper.min.js"></script>
<script src="../scripts/bootstrap.min.js"></script>

<?php
require "../pages/navbar.php";
require "../pages/menu_modals.php";
?>
<!-- javascript data -->
<p id="page_type" class="noshow">form</p>
<p id="saved" class="noshow"><?=$saved;?></p>
<p id="chk_adder" class="noshow"><?=$chkbox_status;?></p>

<div id="form_data">
<form id="user_data" method="post" action="save_page.php">
    <input id="item_no" type="hidden" name="item_no" value="<?=$item_no;?>" />
    <input id="item_nme" type="hidden" name="item_nme" value="<?=$selected;?>" />
    <input type="hidden" name="submitter" value="user_form" />
    <input class="btn btn-success" type="submit" value="Save List">&nbsp;&nbsp;
    <input id="return" class="btn btn-success"
        value="Main Pg"><br /><br />
    <div id="inputs">
        <h5>Create your reference page for <span class="brown"><?=$selected;?>
            </span></h5><br />
        <div id="chkbox"><label for="add_col">Add Boxes</label>&nbsp;&nbsp;
            <input id="add_col" type="checkbox" /></div>
        <table id="refs">
            <thead>
                <tr>
                <?php if (!$default) : ?>
                    <th><textarea id="col1" class="colhdrs"
                        name="col1"><?=$user_hdrs[0];?></textarea></th>
                    <th><textarea id="col2" class="colhdrs"
                        name="col2"><?=$user_hdrs[1];?></textarea></th>
                    <th><textarea id="col3" class="colhdrs"
                        name="col3"><?=$user_hdrs[2];?></textarea></th>
                    <th id="toggled"><textarea id="col4" class="colhdrs"
                        name="col4"><?=$user_hdrs[3];?></textarea></th>
                <?php else: ?> <!-- use blank page headers -->
                    <th><?=$user_hdrs[0];?><textarea id="col1" class="colhdrs"
                        name="col1" placeholder="Your Label Here"></textarea></th>
                    <th><?=$user_hdrs[1];?><textarea id="col2" class="colhdrs"
                        name="col2" placeholder="Your Label Here"></textarea></th>
                    <th><?=$user_hdrs[2];?><textarea id="col3" class="colhdrs"
                        name="col3" placeholder="Your Label Here"></textarea></th>
                    <th id="toggled"><?=$user_hdrs[3];?><textarea id="col4"
                        class="colhdrs" name="col4"
                        placeholder="Your Label Here"></textarea></th>
                <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php for ($j=0; $j<$rowcnt; $j++) : ?>
                <tr>
                    <td><textarea id="r<?=$j;?>" class="b1" name="box1[]"
                        placeholder="Reference or Name"><?=$box1[$j];?></textarea>
                    </td>
                    <td><textarea id="d<?=$j;?>" class="b2" name="box2[]"
                        placeholder="Description/Content"><?=$box2[$j];?></textarea>
                    </td>
                    <td><textarea id="q<?=$j;?>" class="b3" name="box3[]"
                        placeholder="Notes or Qualifiers"><?=$box3[$j];?></textarea>
                    </td>
                    <td class="box4"><textarea id="a<?=$j;?>" class="b4" 
                        name="box4[]"><?=$box4[$j];?></textarea>
                    </td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
        <button id="more_rows" type="button"
            class="btn btn-secondary">Add rows</button>
        <p id="max_row" class="noshow">4</p>

        <!-- template for adding rows and boxes -->
        <template id="rowadder">
            <tr>
                <td><textarea class="b1" name="box1[]"
                    placeholder="Reference or Name"></textarea></td>
                <td><textarea class="b2" name="box2[]"
                    placeholder="Description or Content"></textarea></td>
                <td><textarea class="b3" name="box3[]"
                    placeholder="Notes or Qualifiers"></textarea></td>
                <td class="box4"><textarea id="will_change" class="b4"
                    name="box4[]"></textarea></td>
            </tr>     
        </template>

    </div>
</form>
</div>

<script src="../scripts/user_form.js"></script>
</body>
</html>