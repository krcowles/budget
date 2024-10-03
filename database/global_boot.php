<?php
/**
 * This file is intended to be used as a global php bootstrap file and
 * is to be 'required' by all session-creating php modules.
 * It includes function definitions used by multiple modules as well as 
 * error reporting and logging options, whether in development or 
 * production mode. This file also establishes the PDO object for
 * the session ($pdo).
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */
// Locate site-specific private directories
$documentRoot = $_SERVER['DOCUMENT_ROOT'] . "/";

require $documentRoot . "../bud_settings.php";
require $documentRoot . "vendor/autoload.php";
require $documentRoot . "database/sql_modes.php";
require $documentRoot . "utilities/budgetFunctions.php";
require $documentRoot . "database/errFunctions.php";
require $documentRoot . "database/userFunding.php";
// PHP site recommends following value for future expansion of E_ALL
error_reporting(-1);  // 2147483647 is also suggested on PHP site, both work
if ($HOSTNAME !== '127.0.0.1') { // production environment
    $using = 'server';
    ini_set('log_errors', 1); // (this may be the default anyway)
    ini_set('error_log', '../budgetizer.log');
    // UNCAUGHT error/exception handling:
    set_error_handler('budgetizerErrors'); // errors not using Throwable interface
    set_exception_handler('budgetizerExceptions'); // uncaught exceptions (no try/ctach)
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
// MedRefs virtual subdomain
$msn = sprintf($format, $MHOST, $MDATA);
try {
    $mdo = new PDO($msn, $MUSER, $MPASS, $options);
} catch (PDOException $e) {
    throw new Exception($e->getMessage(), (int)$e->getCode());
}
