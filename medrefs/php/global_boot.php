<?php
/**
 * This file is intended to be used as a global php bootstrap file and
 * is to be 'required' by all session-creating php modules.
 * It includes function definitions used by multiple modules as well as 
 * error reporting and logging options, whether in development or 
 * production mode. This file also establishes the PDO object for
 * the session ($pdo).
 * PHP Version 7.4
 * 
 * @package MedRefs
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */

// Site-specific login and database globals:
define("SITE_URL", "http://localhost");
define("SITE_REF", "MedRefs"); // Site cookie & site session variable name
define("PRIVATE_SETTINGS", '../med_settings.php');
define("RSA_KEYS", "../../medprivate");
define("ERRLOG", "../medref.log"); 
define("ERRHANDLER", "medrefErrors");
define("EXCEPTIONS", "medrefExceptions");
define("ADMIN_EMAIL", "krcowles29@gmail.php");

$root = $_SERVER['DOCUMENT_ROOT'];
// boot code can be called from two diff. directory scenarios
$lead = getcwd() === $root ? '' : '../';
require $lead . "vendor/autoload.php";
require $lead . PRIVATE_SETTINGS; // database connection credentials
require $lead . "database/mode_settings.php";
require $lead . "php/functions.php";
require $lead . "errors/errFunctions.php";

// PHP site recommends following value for future expansion of E_ALL
error_reporting(-1);  // 2147483647 is also suggested on PHP site, both work
if ($HOSTNAME !== '127.0.0.1') { // production environment
    $using = 'server';
    ini_set('log_errors', 1); // (this may be the default anyway)
    ini_set('error_log', ERRLOG);
    // UNCAUGHT error/exception handling:
    set_error_handler(ERRHANDLER); // errors not using Throwable interface
    set_exception_handler(EXCEPTIONS); // uncaught exceptions (no try/ctach)
    // A method for fatal errors that handlers don't catch
    register_shutdown_function("shutdownHandler");
} else { // development
    $using = 'local';
    // In effect, the default UNCAUGHT error/exception handler
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
}

// Establish session database connection
$options = array(
    PDO::ATTR_PERSISTENT => true,
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false
    //PDO::MYSQL_ATTR_INIT_COMMAND => $mode_str
);
$format = 'mysql:host=%s;dbname=%s';
$dsn = sprintf($format, $HOSTNAME, $DATABASE);
try {
    $pdo = new PDO($dsn, $USERNAME, $PASSWORD, $options); // most basic form
} catch (PDOException $e) {
    throw new Exception($e->getMessage(), (int)$e->getCode());
}
