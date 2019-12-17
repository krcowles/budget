<?php
/**
 * This script uses the email address provided by the user and verifies its
 * existence in the 'Users' table. If present, an email is sent to the user
 * providing the user either: 1) his/her login name, or 2) a link to reset
 * the user's password.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../database/global_boot.php";
require "gmail.php";

$etype = filter_input(INPUT_POST, 'parm');
if ($etype === 'uname') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    if (!$email) {
        echo "bad";
        exit;
    }
    $registered
        = $pdo->query('SELECT email FROM `Users`;')->fetchAll(PDO::FETCH_COLUMN);
    if (in_array($email, $registered)) {
        $uname = "SELECT `username` FROM `Users` WHERE `email` = :email;";
        $stmnt = $pdo->prepare($uname);
        $stmnt->execute(["email" => $email]);
        $user = $stmnt->fetch(PDO::FETCH_ASSOC);
        $mail->setFrom('webmaster@budgetizer.epizy.com', 'Do not reply');
        $mail->addAddress($email, 'Budgetizer User');
        $mail->Subject = 'Your Budgetizer User Name';
        $mail->Body = "Your Budgetizer User Name is " . $user['username'];
        $proceed = true;
    } else {
        $proceed = false;
    }
} elseif ($etype === 'passwd') {
    $user = filter_input(INPUT_POST, 'email'); // 'email' is user name in this case
    $umail = "SELECT `email` FROM `Users` WHERE `username` = :uid;";
    $send = $pdo->prepare($umail);
    $send->execute(["uid" => $user]);
    $sendto = $send->fetch(PDO::FETCH_ASSOC);
    if ($sendto) {
        // in order to get the user name in the html without php or javascript:
        $phtml = file_get_contents('passwordLink.html');
        $href = strpos($phtml, "href");
        $half = substr($phtml, 0, $href);
        $whole = $half . 'href="http://budgetizer.epizy.com/admin/renew.php?user=' .
            $user . '">Reset Your Budgetizer Password</a></p>' . PHP_EOL;
        $whole .= PHP_EOL . "</body>" . PHP_EOL . "</html>" . PHP_EOL;
        $email = $sendto['email'];
        file_put_contents('passwordLink.html', $whole);
        $mail->setFrom('webmaster@budgetizer.epizy.com', 'Do not reply');
        $mail->addAddress($email, 'Budgetizer User');
        $mail->Subject = 'Reset Your Budgetizer Password';
        $mail->msgHTML(file_get_contents('passwordLink.html'));
        $proceed = true;
    } else {
        $proceed = false;
    }
}
if ($proceed) {
    @$mail->send();
    echo "ok";
} else {
    echo "nofind";
}

    /*
    if ($mail->send()) {
        echo "ok";
        exit;
    } else {
        $msg = "Error: " . $mail->ErrorInfo;
        exit;
    }

} else {
    echo "nofind";
}
*/
