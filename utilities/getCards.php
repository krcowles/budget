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
$cds = "SELECT * FROM `Cards` WHERE `userid` = :uid;";
$carddat = $pdo->prepare($cds);
$carddat->execute(["uid" => $_SESSION['userid']]);
$cards = $carddat->fetchAll(PDO::FETCH_ASSOC);
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

$ccHtml = '<select class="ccsel"><option value="">SELECT ONE:</option>';
for ($c=0; $c<count($cr); $c++) {
    $ccHtml .= '<option value="' . $cr[$c] . '">' . $cr[$c] . '</option>';
}
$ccHtml .= "</select>";
$dcHtml = '<select class="dcsel">';
for ($d=0; $d<count($dr); $d++) {
    $dcHtml .= '<option value="' . $dr[$d] . '">' . $dr[$d] . '</option>';
}
$dcHtml .= '</select>';
$allCds = array_merge($cr, $dr);
$allCardsHtml = '<select class="allsel"><option value="">SELECT ONE:</option>' .
    '<option value="Check or Draft">Check or Draft</option>';
for ($a=0; $a<count($allCds); $a++) {
    $allCardsHtml .= '<option value="' . $allCds[$a] . '">' .
        $allCds[$a] . '</option>';
}
$allCardsHtml .= '</select>';
