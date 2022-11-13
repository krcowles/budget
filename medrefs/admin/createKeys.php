<?php
/**
 * This file creates RSA keys and stores them in a private
 * directory of the server, above the project's DOCUMENT_ROOT.
 * Used by admin once and only once.
 * PHP Version 7.4
 * 
 * @package MedRefs
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
chdir('../phpseclib1.0.20');
require "Crypt/RSA.php";
$rsa = new Crypt_RSA();
extract($rsa->createKey());

file_put_contents(RSA_KEYS . "/privatekey.pem", $privatekey);
file_put_contents(RSA_KEYS . "/publickey.pem", $publickey);
echo "done";
 