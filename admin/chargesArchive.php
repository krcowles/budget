<?php
/**
 * Archive `Charges` data for the year specified, by creating a new table
 * [Year20xx] and then deleting the data from `Charges`. Export the new table and
 * save as an archive file, not a part of the database. Once saved, the table
 * is dropped. The archived file can be imported later if desired to retrieve
 * data or format reports.
 * PHP Version 7.1
 *
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
require "../database/global_boot.php";

$arch_yr = filter_input(INPUT_GET, 'yr', FILTER_VALIDATE_INT);
$table = "Year" . $arch_yr;

$create = "CREATE TABLE IF NOT EXISTS {$table} (";
$content = <<<ARCH
    `expid` mediumint(9) NOT NULL AUTO_INCREMENT,
    `userid` int(10) NOT NULL,
    `method` varchar(30) NOT NULL,
    `cdname` varchar(30) NOT NULL,
    `expdate` date DEFAULT NULL,
    `expamt` decimal(8,2) NOT NULL,
    `payee` varchar(60) DEFAULT NULL,
    `acctchgd` varchar(60) NOT NULL,
    `paid` varchar(1) DEFAULT NULL,
    PRIMARY KEY (`expid`)
  ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
  ARCH;
  $create .= $content;
  $newArch = $pdo->query($create);

  // Get current Charges data and place into new table
  $lower = $arch_yr . "-01-01";
  $upper = $arch_yr . "-12-31";
  $transfer = "INSERT INTO {$table}
    (`userid`,`method`,`cdname`,`expdate`,`expamt`,`payee`,`acctchgd`,`paid`)
    SELECT
    `userid`,`method`,`cdname`,`expdate`,`expamt`,`payee`,`acctchgd`,`paid`
    FROM `Charges` WHERE `expdate` BETWEEN DATE(?) AND DATE(?);";
  $xfr = $pdo->prepare($transfer);
  $xfr->execute([$lower, $upper]);

  // Delete data from `Charges`
  $delReq = "DELETE FROM `Charges` WHERE `expdate` BETWEEN DATE(?) AND DATE(?);";
  $delete = $pdo->prepare($delReq);
  $delete->execute([$lower, $upper]);

  // Save the new table as an sql file in the database directory
  $archivedb = "export_archive.php?archive=" . $table;
  header("Location: $archivedb");
  exit();
  