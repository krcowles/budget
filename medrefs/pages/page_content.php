<?php
/**
 * General purpose page for displaying page-creation form, or already
 * created pages. This script is ajaxed, and the content is returned
 * as HTML.
 * PHP Version 7.4
 * 
 * @package MedRefs
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require "../../database/global_boot.php";
define("NO_OF_COLS", 4);

$mode      = isset($_POST['mode']) ? filter_input(INPUT_POST, 'mode') : false;
$menu_item = filter_input(INPUT_POST, 'menu');

// when no headers are user-specified:
$defaultHdrItems = [
    "Reference: e.g. Dr.'s Name",
    "Content: e.g. Phone No.",
    "Qualifier: e.g. Specialty",
    "Additional Info: e.g. Location"
];

/**
 * Retrieve UserPage data (by definition, the page exists);
 * this includes both header data ['headers'] and the
 * row data held in the ['box1'] thru ['box4'] fields.
 * NOTE: If the NO_OF_COLS constant changes, the code below
 * will need to be adjusted accordingly
 */
$pageContentReq = "SELECT * FROM `UserPages` WHERE `userid`=? " .
"AND `menu_item`=?;";
$pageContent = $mdo->prepare($pageContentReq);
$pageContent->execute([$_SESSION['userid'], $menu_item]);
$pageData = $pageContent->fetch(PDO::FETCH_ASSOC);
//get user headers
if (empty($pageData['headers'])) {
    // use default headers
    $headerArr = $defaultHdrItems;
} else {
    // retrieve specified headers
    $headerArr = explode("|", $pageData['headers']);
}
// get user's list data
$list = "<tbody>" . PHP_EOL;
$box1list = explode("|", $pageData['box1']);
$box2list = explode("|", $pageData['box2']);
$box3list = explode("|", $pageData['box3']);
$box4list = explode("|", $pageData['box4']);
$show_col4 = empty($pageData['box4']) && empty($pageData['headers']) ? false : true;
if ($mode === 'edit') {
    /**
     * Edit headers and list data are contained in <textarea> elements
     * NOTES:
     *  1. Column 4 is always displayed in case user wishes to make additions
     *  2. If NO_OF_COLS changes, this section must be modified accordingly
     */
    $hdr = [];
    $hdr[0] = <<<COL1
    <thead>
    <tr>
    <th><textarea id="col1" class="colhdrs" name="col1"
        placeholder="Your Label Here">
    COL1;
    $hdr[1] = <<<COL2
    <th><textarea id="col2" class="colhdrs" name="col2"
        placeholder="Your Label Here">
    COL2;
    $hdr[2] = <<<COL3
    <th><textarea id="col3" class="colhdrs" name="col3"
        placeholder="Your Label Here">
    COL3;
    $hdr[3] = <<<COL4
    <th><textarea id="col4" class="colhdrs" name="col4"
        placeholder="Your Label Here">
    COL4;
    $hdr_close = "</textarea></th>";
    $hdr_end = <<<TEND
    </tr>
    </thead>
    TEND;
    // form the editable headers based on 'headers' field in UserPages
    $tbl_headers = '';
    for ($k=0; $k<NO_OF_COLS; $k++) {
        $tbl_headers .= $hdr[$k] . $headerArr[$k] . $hdr_close . PHP_EOL;
    }
    $tbl_headers .= $hdr_end;
    // to simplify creation of table cell definitions for edit mode
    $cell_1   = "<td><textarea id='";
    $cell_2   = "class='tas' name='box";
    $cell_end = "</textarea></td>" . PHP_EOL;
    $cell_del = "</textarea><input type='checkbox' class='rcbs' id='";
    // now create the table rows
    for ($j=0; $j<count($box1list); $j++) {
        $list .= "<tr>" . PHP_EOL;
        // cells
        $list .= $cell_1 . "r{$j}' " . $cell_2 . "1[]'>" . $box1list[$j] . $cell_end;
        $list .= $cell_1 . "d{$j}' " . $cell_2 . "2[]'>" . $box2list[$j] . $cell_end;
        $list .= $cell_1 . "q{$j}' " . $cell_2 . "3[]'>" . $box3list[$j] . $cell_end;
        $list .= $cell_1 . "ed{$j}' " . $cell_2 . "4[]'>" . $box4list[$j] . 
            $cell_del . "rcb" . $j . "' /></td>";
        $list .= "</tr>" . PHP_EOL;
    }
    $list .= "</tbody>";
}

if ($mode === 'display') {
    // headers:
    if (empty($pageData['headers'])) {
        // use default headers
        $headerArr = $defaultHdrItems;
    } else {
        // retrieve headers
        $headerArr = explode("|", $pageData['headers']);
    }
    // form table headers
    $tbl_headers = '<thead>' . PHP_EOL . '<tr>' . PHP_EOL;
    foreach ($headerArr as $hdr) {
        $tbl_headers .= "<th>{$hdr}</th>" . PHP_EOL;
    }
    $tbl_headers .= "</tr>" . PHP_EOL . "</thead>" . PHP_EOL;
    // now create th table rows
    for ($j=0; $j<count($box1list); $j++) {
        $list .= "<tr>" . PHP_EOL;
        $list .= "<td class='td1'>$box1list[$j]</td>" . PHP_EOL;
        $list .= "<td class='td2'>$box2list[$j]</td>" . PHP_EOL;
        $list .= "<td class='td3'>$box3list[$j]</td>" . PHP_EOL;
        $list .= "<td class= 'td4'>$box4list[$j]</td>" . PHP_EOL;
        $list .= "</tr>" . PHP_EOL;
    }
    $list .=  "</tbody>" . PHP_EOL;
}
echo $tbl_headers . $list;
