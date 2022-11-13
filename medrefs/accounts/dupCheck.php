<?php
/**
 * Verify that the candidate user has selected neither a duplicate
 * username nor email address.
 * PHP Version 7.4
 * 
 * @package MedRefs
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../php/global_boot.php";
verifyAccess('ajax');

$name = isset($_POST['username']) ? filter_input(INPUT_POST, 'username') : false;
$mail = isset($_POST['email']) ? filter_input(INPUT_POST, 'email') : false;
$match = "NO";
$getDB_dataReq = "SELECT `username`,`email` FROM `Users`;";
$users = $pdo->query($getDB_dataReq)->fetchAll(PDO::FETCH_KEY_PAIR);
if (count($users) === 0) {
    echo $match;
    exit;
} 
$registeredNames = array_keys($users);
/* --- IF USERNAME IS ENCRYPTED ---
// decrypt usernames:
chdir('../phpseclib1.0.20');
require "Crypt/RSA.php";
$publickey  = file_get_contents('../../medprivate/publickey.pem');
$rsa = new Crypt_RSA();
*/
$unames = [];
//$rsa->loadKey($publickey);

foreach ($registeredNames as $crypto) {
    $uname = $crypto;
    /* --- ENCRYPTED USERNAMES
    $newcipher = hex2bin($uname);
    $orgname = $rsa->decrypt($newcipher);
    array_push($unames, $orgname);
    */
    array_push($unames, $uname);
}
if ($name !== false && in_array($name, $unames)) {
    $match = 'YES';
} else if ($mail !== false) {
    foreach ($users as $key => $email) {
        if ($email === $mail) {
            $match = "YES";
            break;
        }
    }
}
echo $match;
