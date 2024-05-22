<?php
session_start();
require '../admin/EventLogger.php'; // Adjust the path as per your file structure

// Check if the user is already logged in, if not, redirect to the login page
if (empty($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Initialize the EventLogger
$eventLogger = new EventLogger();
// Log user logout event
$eventLogger->logLogoutEvent($_SESSION["user_id"]);

// Destroy the session
session_destroy();

// Redirect the user to the login page
header("Location: login.php");
exit();
?>
