<?php
/**
 * This script will only be executed once in order to create the tables
 * needed by budgetizer to manage user budgets.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require_once "../database/global_boot.php";

$cards = <<<CD
CREATE TABLE Cards (
    cdindx  SMALLINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user    VARCHAR(30) NOT NULL,
    cdname  VARCHAR(30) NOT NULL,
    type    VARCHAR(6) NOT NULL
);
CD;
$pdo->query($cards);
$budgets = <<<BUD
CREATE TABLE Budgets (
    id      MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user    VARCHAR(30) NOT NULL,
    budname VARCHAR(40) NOT NULL,
    budpos  SMALLINT NOT NULL,
    status  VARCHAR(1) NOT NULL,
    budamt  SMALLINT NOT NULL,
    prev0   DECIMAL(8,2) NULL,
    prev1   DECIMAL(8,2) NULL,
    current DECIMAL(8,2) NOT NULL,
    autopay VARCHAR(30) NULL,
    moday   TINYINT NULL,
    autopd  VARCHAR(10) NULL,
    funded  SMALLINT NULL
);
BUD;
$pdo->query($budgets);
$charges = <<<EXP
CREATE TABLE Charges (
    expid   MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user    VARCHAR(30) NOT NULL,
    method  VARCHAR(30) NULL,
    expdate DATE NULL,
    expamt  DECIMAL(8,2) NOT NULL,
    payee   VARCHAR(50) NULL,
    recon   VARCHAR(1) NULL
);
EXP;
$pdo->query($charges);
