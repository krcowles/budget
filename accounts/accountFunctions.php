<?php
/**
 * This module contains the functions associated with checking for failed
 * logins.
 * PHP Version 7.4
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */

/**
 * The following function gets the visitor's machine IP even when going through
 * a proxy server. Copied from:
 * https://www.w3adda.com/blog/how-to-get-user-ip-address-in-php
 * 
 * @return string $ip
 */
function getIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
/**
 * Update the value of `fails` in the `Locks` table, and if it is equal to
 * or greater than 3, set the `lockout` time in the table to enable js to
 * check current time against latest failed attempt.
 * 
 * @param integer $failed_attempts Number of times a failed login has occurred
 * @param string  $ip              User's IP Address
 * @param PDO     $pdo             PDO Class for connecting to database
 * 
 * @return null
 */
function updateFailures($failed_attempts, $ip, $pdo)
{
    if ($failed_attempts >= 3) {
        $latest = new DateTime("now", new DateTimeZone('America/Denver'));
        $latest->modify('+ 1 hour'); // current wait time = 1 hour
        $unlock =  $latest->format('Y-m-d H:i:s'); // MySQL DATETIME requires string
        $updateReq = "UPDATE `Locks` SET `fails`=?, `lockout`=? WHERE `ipaddr`=?;";
        $update = $pdo->prepare($updateReq);
        $update->execute([$failed_attempts, $unlock, $ip]);
    } else {
        $updateReq = "UPDATE `Locks` SET `fails`=? WHERE `ipaddr`=?;";
        $update = $pdo->prepare($updateReq);
        $update->execute([$failed_attempts, $ip]);
    }
    return;
} 
