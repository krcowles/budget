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
$root = $_SERVER['DOCUMENT_ROOT'];
// boot can be called from two diff. directory scenarios
$lead = getcwd() === $root ? '' : '../';

require $lead . "vendor/autoload.php";
require $lead . "database/settings.php";
require $lead . "database/sql_modes.php";
//require $lead . "database/errFunctions.php";
// PHP site recommends following value for future expansion of E_ALL
error_reporting(-1);  // 2147483647 is also suggested on PHP site, both work
/*if ($appMode === 'production') {
    ini_set('log_errors', 1); // (this may be the default anyway)
    ini_set('error_log', '../ktesa.log');

// UNCAUGHT error/exception handling:
set_error_handler('ktesaErrors'); // errors not using Throwable interface
set_exception_handler('ktesaExceptions'); // uncaught exceptions (no try/ctach)
// A method for fatal errors that handlers don't catch
register_shutdown_function("shutdownHandler");

//} else { // development
    /**
     * In this mode, no error_log is specified, so syslog could be used;
     * However, with whoops, there is no syslog, thus the following three
     * statements are not needed.
     * Use them if/when whoops is not available.
     */
    //ini_set('display_errors', "1"); // default is off i.e. 'production'
    //ini_set('display_startup_errors', 1);  // should never be 'on' in production
    //ini_set('log_errors', 1);

// In effect, the default UNCAUGHT error/exception handler
$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

//}

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
    $pdo = new PDO($dsn, $USERNAME, $PASSWORD); // most basic form
} catch (PDOException $e) {
    throw new Exception($e->getMessage(), (int)$e->getCode());
}
