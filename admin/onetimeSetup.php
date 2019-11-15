<?php
/**
 * To get it going...
 * PHP Version 7.1
 * 
 * @package Global_Boot
 * @author  Tom Sandberg and Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../database/test.php";
// ------- CREATE  -------
/*
$req = <<<EOR
CREATE TABLE `Users` (
    `uid` int(10) NOT NULL AUTO_INCREMENT,
    `email` varchar(60) NOT NULL,
    `username` varchar(40) NOT NULL,
    `password` varchar(100) NOT NULL,
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
ALTER TABLE Users
ADD COLUMN `passwd_expire` DATE NULL AFTER `password`;
QUERY;
$results = $pdo->query($req);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Global Test</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
        content="Rolling 3-month budget tracker" />
    <meta name="author" content="Ken Cowles" />
    <meta name="robots" content="nofollow" />
</head>

<body>
