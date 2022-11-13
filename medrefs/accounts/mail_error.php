<?php
/**
 * This function posts the error message from sendmail
 * PHP Version 7.2
 * 
 * @package MedRefs
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
$message = filter_input(INPUT_POST, 'err');
file_put_contents('mail_err.txt', $message, FILE_APPEND);
