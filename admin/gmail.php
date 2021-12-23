<?php
/**
 * This script is required to generate mail via gmail, since the InfinityFree
 * site restricts usage of php mail() for free uers.
 * PHP Version 7.1
 *
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
date_default_timezone_set('America/Denver');
require '../PHPMailer/PHPMailerAutoload.php';
$mail = new PHPMailer;
$mail->isSMTP();
/*
 * Server Configuration
 */
$mail->Host = 'smtp.gmail.com'; // Which SMTP server to use.
$mail->Port = 587; // Which port to use, 587 is the default port for TLS security.
$mail->SMTPSecure = 'tls'; // Which security method to use. TLS is most secure.
$mail->SMTPAuth = true;
$mail->Username = "krcowles29@gmail.com";
$mail->Password = GMAIL_ID; // App Specific Password.
