<?php
/**
 * This module contains error handling functions defined for the project.
 * PHP Version 7.1
 * 
 * @package Budget
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */

/**
 * Exception traces seem to get truncated, which isn't helpful! This
 * function expands the information so that it is not truncated...
 * https://stackoverflow.com/questions/1949345/how-can-i-get-the-full-string-of-php-s-gettraceasstring
 * 
 * @param Exception $exception The exception thrown;
 * 
 * @return string $rtn
 */
function getExceptionTraceAsString($exception)
{
    $rtn = "";
    $count = 0;
    foreach ($exception->getTrace() as $frame) {
        $args = "";
        if (isset($frame['args'])) {
            $args = array();
            foreach ($frame['args'] as $arg) {
                if (is_string($arg)) {
                    $args[] = "'" . $arg . "'";
                } elseif (is_array($arg)) {
                    $args[] = "Array";
                } elseif (is_null($arg)) {
                    $args[] = 'NULL';
                } elseif (is_bool($arg)) {
                    $args[] = ($arg) ? "true" : "false";
                } elseif (is_object($arg)) {
                    $args[] = get_class($arg);
                } elseif (is_resource($arg)) {
                    $args[] = get_resource_type($arg);
                } else {
                    $args[] = $arg;
                }
            }
            $args = join(", ", $args);
        }
        $current_file = "[internal function]";
        if (isset($frame['file'])) {
            $current_file = $frame['file'];
        }
        $current_line = "";
        if (isset($frame['line'])) {
            $current_line = $frame['line'];
        }
        $rtn .= sprintf(
            "#%s %s(%s): %s(%s)\n",
            $count,
            $current_file,
            $current_line,
            $frame['function'],
            $args
        );
        $count++;
    }
    return $rtn;
}
/**
 * This function establishes production mode error handling, which
 * will present a user-friendly error page. Uncaught errors will be
 * logged to ktesa.log, and an email sent to site masters.
 * 
 * @param string $errno   The error number reported back by the error
 * @param string $errstr  The actual error message reported
 * @param string $errfile The file name in which the error occurred
 * @param string $errline The line in the above file in which error occurred
 * 
 * @return null
 */
function budgetizerErrors($errno, $errstr, $errfile, $errline)
{
    $lastTrace = getExceptionTraceAsString(new Exception);
    error_log($lastTrace);
    errorEmail($lastTrace);
    errorPage();
    
}
/**
 * This is the production mode exception handler, also presenting 
 * exception data to the logger and a user-friendly page to the user.
 * Note that execution halts automatically after the uncaught exception.
 * 
 * @param object $exception The exception object
 * 
 * @return null
 */
function budgetizerExceptions($exception)
{
    $message = "An uncaught exception occurred:\n" .
        "Code: " . $exception->getCode() . 
        " in file " . $exception->getFile() .
        " at line " . $exception->getLine() . "\n" .
        $exception->getMessage() . "\n" .
        "TRACE: " . $exception->getTraceAsString();
    error_log($message);
    errorEmail($message);
    errorPage();
} 
/**
 * This is a custom handler to catch the ugly parse/compile errors et al
 * that don't otherwise get caught in error handlers or in whoops.
 * 
 * @return null
 */
function shutdownHandler() //will be called when php script ends.
{
    $lasterror = error_get_last();
    if (!empty($lasterror)) {
        switch ($lasterror['type'])
        {
        case E_ERROR:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
        case E_USER_ERROR:
        case E_RECOVERABLE_ERROR:
        case E_CORE_WARNING:
        case E_COMPILE_WARNING:
        case E_PARSE:
            $error = "[SHUTDOWN] lvl:" . $lasterror['type'] .
                " | msg:" . $lasterror['message'] . " | file:" .
                $lasterror['file'] . " | ln:" . $lasterror['line'];
            shutdownError($error, "fatal");
        }
    }
}
/**
 * This function is called by the shutdown handler and receives 
 * a custom constructed error message from it. It is constructed
 * as a general-purpose call which could receive non-fatal errors.
 * 
 * @param string $errmsg the message about the fatal error
 * @param string $errlvl the level of the error
 * 
 * @return null
 */
function shutdownError($errmsg, $errlvl) 
{
    error_log($errmsg);
    errorEmail($errmsg);
    errorPage();
}
/**
 * This is the user-friendly error page presented to the user
 * 
 * @return null
 */
function errorPage()
{
    $user_error_page = "../php/user_error_page.php";
    header("Location: {$user_error_page}");
}
/**
 * This function generates an email to the current admin to report the error
 * encountered by a user.
 * 
 * @param string $msg The error message and trace
 * 
 * @return null;
 */
function errorEmail($msg)
{
    include "../accounts/gmail.php";
    date_default_timezone_set('America/Denver');
    $user = isset($_SESSION['username']) ? $_SESSION['username'] : 'no_user';
    $subject = "Production error encountered";
    $message = "User " . $user . " encountered the following error on " .
        date("Y-m-d G:i:s") . PHP_EOL . $msg;
    // Mail it
    $mail->isHTML(true);
    $mail->setFrom('admin@nmhikes.com', 'Do not reply');
    $mail->addAddress(ADMIN, 'Admin');
    $mail->Subject = $subject;
    $mail->Body = $message;
    @$mail->send();
}