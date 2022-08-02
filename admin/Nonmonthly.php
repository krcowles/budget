<?php
/**
 * Setup the 'Irreg' table in the db for Non-Monthlies account
 * PHP Version 7.4
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license None to date
 */
require "../database/global_boot.php";

$nmacct = <<<NM
CREATE TABLE Irreg (
    record  INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    userid  INT NOT NULL,
    item    VARCHAR(50) NOT NULL,
    freq    VARCHAR(20) NOT NULL,
    amt     DECIMAL(7,2) NOT NULL,
    first   VARCHAR(10) NOT NULL,
    SA_yr   VARCHAR(4) NOT NULL
);
NM;
$pdo->query("DROP TABLE IF EXISTS Irreg;");
$pdo->query($nmacct);
echo "DONE!";
