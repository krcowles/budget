<?php
/**
 * This script will modify the user's menu according to the
 * user action specified by the menu manager modals.
 * PHP Version 7.4
 * 
 * @package MedRefs
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require "../../database/global_boot.php";

$action = filter_input(INPUT_POST, 'action');
$data   = isset($_POST['data'])   ? filter_input(INPUT_POST, 'data') : false;
$select = isset($_POST['select']) ? filter_input(INPUT_POST, 'select') : false;

$settingsReq = "SELECT `menu` FROM `Settings` WHERE `userid`=?;";
$settings = $mdo->prepare($settingsReq);
$settings->execute([$_SESSION['userid']]);
$menuRow = $settings->fetch(PDO::FETCH_ASSOC);
$menu_array = explode("|", $menuRow['menu']);

$menu_change = true;
// menu changes
if ($action === 'add') {
    array_push($menu_array, $data);
    $new_menu = implode("|", $menu_array);
   
} elseif ($action === 'rename') {
    $old_loc = array_search($select, $menu_array);
    if ($old_loc === false) {
        echo "Item not located in array";
        exit;
    }
    $replacement = [$data];
    array_splice($menu_array, $old_loc, 1, $replacement);
    $new_menu = implode("|", $menu_array);
} elseif ($action === 'delete') {
    $del_item = array_search($select, $menu_array);
    array_splice($menu_array, $del_item, 1);
    $new_menu = implode("|", $menu_array);
}
// active item change
if ($action === 'home') {
    $menu_change = false;
    $newhome = array_search($select, $menu_array) + 1;
    $mod_activeReq = "UPDATE `Settings` SET `active`=? WHERE `userid`=?;";
    $mod_active = $mdo->prepare($mod_activeReq);
    $mod_active->execute([$newhome, $_SESSION['userid']]);
}
if ($menu_change) {
    $mod_menuReq = "UPDATE `Settings` SET `menu`=? WHERE `userid`=?;";
    $mod_menu = $mdo->prepare($mod_menuReq);
    $mod_menu->execute([$new_menu, $_SESSION['userid']]);
}

echo "ok";