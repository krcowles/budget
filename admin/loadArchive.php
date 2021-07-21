<?php
/**
 * This script will load the archive data prescribed by input
 * PHP Version 7.1
 * 
 * @package Admin
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../database/global_boot.php";

$arch = filter_input(INPUT_GET, 'arch');

// Is there currently any data from the archive year in the `Charges` table?
// If so, the archive has been previously loaded (or never archived!)
$datesReq = "SELECT EXTRACT(YEAR FROM expdate) FROM `Charges`;";
$dates = $pdo->query($datesReq)->fetchAll(PDO::FETCH_COLUMN);
if (in_array($arch, $dates)) {
    echo "Previously loaded";
    exit;
}
$table = 'Year' . $arch;
$archfile = '../database/' . $table . '.sql';
if (file_exists($archfile)) {
    $arch2load = file_get_contents($archfile);
    if (strpos($arch2load, 'CREATE') === false) {
        echo "No create";
        exit;
    }
    $insert = strpos($arch2load, "INSERT");
    if ($insert === false) {
        echo "No insert";
        exit;
    }
} else {
    echo "No archive exists";
    exit;
}
// only do this once!
$testReq = 'SHOW TABLES LIKE "' . $table . '";';
$test = $pdo->query($testReq)->fetch();
if (!$test) {
    $createReq = trim(substr($arch2load, 0, $insert-1));
    $insertReq = trim(substr($arch2load, $insert));
    $pdo->query($createReq);
    $pdo->query($insertReq);
    // move data back into `Charges` table
    $restoreReq = "INSERT INTO `Charges`
        (`userid`,`method`,`cdname`,`expdate`,`expamt`,`payee`,`acctchgd`,`paid`)
        SELECT
        `userid`,`method`,`cdname`,`expdate`,`expamt`,`payee`,`acctchgd`,`paid`
        FROM {$table};";
    $pdo->query($restoreReq);
    echo "Done";
} else {
    echo "Previously loaded";
}
