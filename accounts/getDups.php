<?php
/**
 * Get the list of current users and verify new submission is unique
 * with respect to both username and email
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../database/global_boot.php";
chdir('../phpseclib1.0.20');
require "Crypt/RSA.php";
$publickey  = file_get_contents('../../budprivate/publickey.pem');
$rsa = new Crypt_RSA();
$rsa->loadKey($publickey);

$name = isset($_POST['username']) ? filter_input(INPUT_POST, 'username') : false;
$mail = isset($_POST['email']) ? filter_input(INPUT_POST, 'email') : false;
$match = "NO";
$getDB_dataReq = "SELECT `username`,`email` FROM `Users`;";
$users = $pdo->query($getDB_dataReq)->fetchAll(PDO::FETCH_KEY_PAIR); 

if ($name !== false) {
    // Get current list of usernames: 
    foreach ($users as $key => $email) {
        $cipher = hex2bin($key);
        $existing = $rsa->decrypt($cipher);
        if ($existing == $name) {
            $match = "YES";
            break;
        }
    }
} else if ($mail !== false) {
    foreach ($users as $key => $email) {
        if ($email === $mail) {
            $match = "YES";
            break;
        }
    }
}
echo $match;
