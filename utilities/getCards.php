<?php
/**
 * This utility queries the 'Cards' table to extract credit and debit cards,
 * and store them in arrays for use by the caller.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require_once "../database/global_boot.php";

$cr = [];
$dr = [];
$cds = "SELECT * FROM `Cards` WHERE `user` = :user;";
$carddat = $pdo->prepare($cds);
$carddat->execute(["user" => $user]);
$cards = $carddat->fetchALL(PDO::FETCH_ASSOC);
$credit_cards = count($cards) > 0 ? true : false;
if ($credit_cards) {
    foreach ($cards as $card) {
        if ($card['type'] == 'Debit') {
            array_push($dr, $card['cdname']);
        } elseif ($card['type'] == 'Credit') {
            array_push($cr, $card['cdname']);
        }
    }
}
