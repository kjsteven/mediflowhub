<?php 

// Function to check if the user has the 'admin' role
function check_admin_role() {
    if (isset(  $_SESSION["role"]) && $_SESSION['role'] === 'Admin') {
        return true;
    } else {
        return false;
    }
}


?>
