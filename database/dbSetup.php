<?php
/**
 * To get it going...
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Tom Sandberg and Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
//require_once "global_boot.php";
// ------- CREATE  -------

/*
$req = <<<EOR
CREATE TABLE `Users` (
    `uid` int(10) NOT NULL AUTO_INCREMENT,
    `email` varchar(60) NOT NULL,
    `username` varchar(40) NOT NULL,
    `LCM` varchar(12) NULL,
    `password` varchar(100) NOT NULL,
    `passwd_expire` date NULL,
    PRIMARY KEY (`uid`)
);
EOR;
*/
// ------- SHOW TABLE STRUCTURE -------
/*
$req = "SHOW CREATE TABLE Users";
*/
// ------- INSERT DATA INTO TABLE ------
/*
$req = 'INSERT INTO Users VALUES (1, "krcowles29@gmail.com", "kenc", "quatzl"),' .
       '(2, "abqgal13@icloud.com", "karen", "jimmy");';
*/
// ------- ADD COLUMN(S) -------

$req = <<<QUERY
ALTER TABLE `Users`
ADD COLUMN `setup` Varchar(7) NULL AFTER `username`;
QUERY;
// setup=> 'none', 'budget', 'cards', 'charges', 'all'

/*
$req = <<<BUD
CREATE TABLE Budgets (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `user` varchar(30) NOT NULL,
    `budname` varchar(60) NOT NULL,
    `budpos`  smallint NOT NULL,
    `status`  varchar(1) NOT NULL,
    `budamt`  smallint NULL,
    `prev0`   decimal(8,2) NULL,
    `prev1`   decimal(8,2) NULL,
    `current` decimal(8,2) NULL,
    `autopay` varchar(30) NULL,
    `moday`   tinyint NULL,
    `autopd`  varchar(10) NULL,
    `funded`  smallint NULL,
    PRIMARY KEY (`id`)
);
BUD;
*/
/*
$req = <<<CDS
CREATE TABLE Cards (
    `cdindx` smallint NOT NULL AUTO_INCREMENT,
    `user`   varchar(30),
    `cdname` varchar(30),
    `type`   varchar(6),
    PRIMARY KEY (`cdindx`)
);
CDS;
*/
/*
$drop = "DROP TABLE `Charges`;";
$pdo->query($drop);

$req = <<<CHGS
CREATE TABLE Charges (
    `expid` mediumint NOT NULL AUTO_INCREMENT,
    `user`  varchar(30) NOT NULL,
    `method` varchar(10) NOT NULL,
    `cdname` varchar(30) NOT NULL,
    `expdate` date,
    `expamt` decimal(8,2) NOT NULL,
    `payee`  varchar(60),
    `acctchgd` varchar(60) NOT NULL,
    `paid`  varchar(1),
    PRIMARY KEY (`expid`)
);
CHGS;
*/

$results = $pdo->query($req);
