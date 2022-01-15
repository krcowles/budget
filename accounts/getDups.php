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

$name = isset($_POST['username']) ? filter_input(INPUT_POST, 'username') : false;
$mail = isset($_POST['email']) ? filter_input(INPUT_POST, 'email') : false;
$match = "NO";
$getDB_dataReq = "SELECT `username`,`email` FROM `Users`;";
$users = $pdo->query($getDB_dataReq)->fetchAll(PDO::FETCH_KEY_PAIR); 

if ($name !== false) {
    // Get current list of usernames: 
    foreach ($users as $key => $email) {
        if ($key == $name) {
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
