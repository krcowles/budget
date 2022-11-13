<?php
/**
 * Save the incoming menu item number as the default starting point for
 * displaying a page
 * PHP Version 7.4
 * 
 * @package MedRefs
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
session_start();
require "../../database/global_boot.php";

$getMenuReq = "SELECT `menu` FROM `Settings` WHERE `userid` = ?;";
$getMenu = $mdo->prepare($getMenuReq);
$getMenu->execute([$_SESSION['userid']]);
$menu_str = $getMenu->fetch(PDO::FETCH_ASSOC);
$menu_items = $menu_str['menu'];
$items = explode("|", $menu_items);
$menu_max = count($items);

$save = filter_input(INPUT_GET, 'no', FILTER_VALIDATE_INT);
if ($save === false || $save < 1 || $save > $menu_max) {
    echo "Bad menu item number";
    exit;
}

$saveNoReq = "UPDATE `Settings` SET `active`={$save};";
$saveNo = $mdo->query($saveNoReq);
echo "ok";
