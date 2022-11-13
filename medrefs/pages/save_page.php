<?php
/**
 * This accepts posted data from user_form.php or from and creates a reference
 * page for display of the user's data.
 * PHP Version 7.4
 * 
 * @package MedRefs
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require "../../database/global_boot.php";

$item_no   = filter_input(INPUT_POST, 'item_no');
$item_nme  = filter_input(INPUT_POST, 'item_nme');
$submitter = isset($_POST['submitter']) ?
    filter_input(INPUT_POST, 'submitter') : 'main';
$return    = $submitter === 'main' ? 
    "Location: main.php" :
    "Location: user_form.php?menu={$item_no}&item={$item_nme}&save=y";

$box1  = $_POST['box1'];
$box2  = $_POST['box2'];
$box3  = $_POST['box3'];
$box4  = $_POST['box4'];
$c1hdr = $_POST['col1'];
$c2hdr = $_POST['col2'];
$c3hdr = $_POST['col3'];
$c4hdr = $_POST['col4'];
// number of characters in corresponding boxes
$len1 = [];
$len2 = [];
$len3 = [];
$len4 = [];
// box contents where box1 is not empty
$db_box1 = [];
$db_box2 = [];
$db_box3 = [];
$db_box4 = [];
// save any specified user's headers for columns:
$hdr[0] = !empty($c1hdr) ? $c1hdr : '';
$hdr[1] = !empty($c2hdr) ? $c2hdr : '';
$hdr[2] = !empty($c3hdr) ? $c3hdr : '';
$hdr[3] = !empty($c4hdr) ? $c4hdr : '';
if ($hdr[0] == '' && $hdr[1] == '' && $hdr[2] == '' & $hdr[3] == '') {
    $headers = null;
} else {
    foreach ($hdr as &$label) {
        if (empty($label)) {
            $label = 'Not specified';
        }
    }
    $headers = implode("|", $hdr);
}


// table row including <td> cell assignments
$rows = [];
for ($j=0; $j<count($box1); $j++) {
    if (!empty($box1[$j])) {
        $entry  = '<td>' . filter_var($box1[$j]) . '</td>';
        $entry .= '<td>' . filter_var($box2[$j]) . '</td>';
        $entry .= '<td>' . filter_var($box3[$j]) . '</td>';
        $entry .= '<td>' . filter_var($box4[$j]) . '</td>';
        array_push($rows, $entry);
        array_push($len1, strlen($box1[$j]));
        array_push($len2, strlen($box2[$j]));
        array_push($len3, strlen($box3[$j]));
        array_push($len4, strlen($box4[$j]));
        array_push($db_box1, $box1[$j]);
        array_push($db_box2, $box2[$j]);
        array_push($db_box3, $box3[$j]);
        array_push($db_box4, $box4[$j]);
    }
}
//print_r($rows);
$row_width
    = 1.2 * (max($len1) + max($len2) + max($len3) + max($len4)); // 1.2 => margin
$width = 12 * $row_width; // 12 => approx px per char.
$dbbox1 = implode("|", $db_box1);
$dbbox2 = implode("|", $db_box2);
$dbbox3 = implode("|", $db_box3);
$dbbox4 = implode("|", $db_box4);
$find_rowReq = "SELECT * FROM `UserPages` WHERE `userid`=? AND `menu_item`=?;";
$find_row = $mdo->prepare($find_rowReq);
$find_row->execute([$_SESSION['userid'], $item_no]);
$row_exists = $find_row->fetch(PDO::FETCH_ASSOC);
if ($row_exists === false) {
    // update Settings with this menu item as 'active'
    $newActiveReq = "UPDATE `Settings` SET `active`={$item_no};";
    $newActive = $mdo->query($newActiveReq);
    // add the page to UserPages
    $setPageReq = "INSERT INTO `UserPages` (`userid`,`menu_item`,`headers`,`box1`," .
        "`box2`,`box3`,`box4`,`row_length`) VALUES (?,?,?,?,?,?,?,?);";
    $vals = array(
        $_SESSION['userid'], 
        $item_no,
        $headers,
        $dbbox1,
        $dbbox2,
        $dbbox3,
        $dbbox4,
        $width
    );
} else {
    $setPageReq = "UPDATE `UserPages` SET `headers`=?,`box1`=?,`box2`=?,`box3`=?," .
        "`box4`=?,`row_length`=? WHERE `userid`=? AND `menu_item`=?;";
    $vals = array(
        $headers,
        $dbbox1,
        $dbbox2,
        $dbbox3,
        $dbbox4,
        $width,
        $_SESSION['userid'],
        $item_no
    );
}
$setPage = $mdo->prepare($setPageReq);
$setPage->execute($vals);

header($return);
