<?php
require '../admin/ErrorLogger.php';


error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize the ErrorLogger
$errorLogger = new ErrorLogger();

// Custom error handler
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    global $errorLogger;
    $message = "Error [$errno]: $errstr";
    $errorLogger->log($message, $errfile, $errline);
    return true; // Prevent PHP default error handler
}

// Set the custom error handler
set_error_handler('customErrorHandler');

// Custom exception handler
function customExceptionHandler($exception) {
    global $errorLogger;
    $message = "Uncaught exception: " . $exception->getMessage();
    $errorLogger->log($message, $exception->getFile(), $exception->getLine());
}

// Set the custom exception handler
set_exception_handler('customExceptionHandler');

// Example to trigger an error and exception
trigger_error("This is a test error", E_USER_NOTICE);
throw new Exception("This is a test exception");
?>
