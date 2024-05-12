<?php
session_start();

// Check if the user is already logged in, if not, redirect to the login page
if (empty($_SESSION["doctor_id"])) {
    header("Location: doctor-login.php");
    exit();
}

// Destroy the session
session_destroy();

// Redirect the user to the login page
header("Location: doctor-login.php");
exit();
?>
