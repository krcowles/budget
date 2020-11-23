<?php
/**
 * This file should reside one level above "DOCUMENT_ROOT" to maintain
 * security of the database credentials - however, this site will not
 * allow uploads at the level. Therefore, for this site, the settings
 * reside, for now, in the 'database' directory.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
$PORT = "3306";
$CHARSET = "UTF8";
$devhost = $_SERVER['SERVER_NAME'] == 'localhost' ? true : false;
if ($devhost) { // LOCAL MACHINE
    $HOSTNAME = "127.0.0.1";
    $USERNAME = "root";
    $PASSWORD = "root";
    $DATABASE = "epiz_24776673_BudgdetData"; // omitted "7" in epiz_name locally
    
} else {
    $HOSTNAME = "sql204.epizy.com";
    $PASSWORD = "qkakFybzKN9X";
    $USERNAME = "epiz_24776673";
    $DATABASE = "epiz_24776673_BudgdetData"; // mistyped db name during creation:
}
