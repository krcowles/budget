<?php
require "../database/global_boot.php";

chdir('../phpseclib1.0.20');
require "Crypt/RSA.php";
$privatekey = file_get_contents('../../budprivate/privatekey.pem');
$rsa = new Crypt_RSA();
$rsa->loadKey($privatekey);
$e1 = $rsa->encrypt('krcowles29@gmail.com');
$e2 = $rsa->encrypt('tonks130@gmail.com');
$addr1 = bin2hex($e1);
$addr2 = bin2hex($e2);
$updateReq = "UPDATE `Users` SET `email`=? WHERE `uid`=?;";
$statmnt = $pdo->prepare($updateReq);
$statmnt->execute([$addr1, '4']);
$update2Req = "UPDATE `Users` SET `email`=? WHERE `uid`=?;";
$stat2 = $pdo->prepare($update2Req);
$stat2->execute([$addr2, '11']);
echo "DONE";
