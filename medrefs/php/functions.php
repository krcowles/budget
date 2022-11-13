<?php
/**
 * This module contains the functions required to carry out various
 * account management tasks.
 * PHP Version 7.4
 * 
 * @package Ktesa
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
/**
 * This function is invoked to ensure that the proper method was invoked,
 * and a result of an attempt to run the script as intended.
 * 
 * @param string $method The type of method being invoked by the caler
 * 
 * @return null;
 */
function verifyAccess($method)
{
    $msg = "Access denied to this script";
    if ($method === 'ajax') {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        ) {
            return;
        } else {
            die($msg);
        }
    }
    if ($method === 'post') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return;
        } else {
            die($msg);
        }
    }
    if ($method === "GET") {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return;
        } else {
            die($msg);
        }
    }
}
