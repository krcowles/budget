<?php
/**
 * This simple script utilizes the phpseclib to create and save RSA keys
 * The basic idea came from StackOverflow:
 * https://stackoverflow.com/questions/11470779/rsa-decryption-using-private-key
 * PHP Version 7.4
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../database/global_boot.php";

chdir('../phpseclib1.0.20');
require "Crypt/RSA.php";

$rsa = new Crypt_RSA();
extract($rsa->createKey());
file_put_contents('../database/puclasskey.txt', $publickey);
file_put_contents('../database/prclasskey.txt', $privatekey);
echo "DONE";
