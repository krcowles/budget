<?php
require "../database/global_boot.php";

$updteBudReq = "UPDATE `Charges` SET `userid` = 4 WHERE `user` = 'krc';";
$updte = $pdo->query($updteBudReq);
$updteKarenReq = "UPDATE `Charges` SET `userid` = 11 WHERE `user` = 'Albuquerque Gal';";
$updte = $pdo->query($updteKarenReq);
$updteKarenReq = "UPDATE `Charges` SET `userid` = 12 WHERE `user` = 'Mrsbolgat';";
$updte = $pdo->query($updteKarenReq);
