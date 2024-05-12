<?php

function start_secure_session() {   
    session_start();

    $session_timeout = 1800; // 30 minutes in seconds
    $warning_threshold = 300; // 5 minutes in seconds (adjust as needed)

    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)) {
        // Session expired, destroy it
        session_unset();
        session_destroy();

        // Redirect to login page
        header("Location: ../users/login.php");
        exit();
    } else {
        // Update last activity time
        $_SESSION['last_activity'] = time();

        // Set a secure, HTTP-only cookie
        if (!isset($_COOKIE['my_session_cookie'])) {
            $session_id = bin2hex(random_bytes(32)); // Generate a secure session ID
            setcookie('my_session_cookie', $session_id, time() + $session_timeout, '/', '', true, true); // Set the cookie
            // You may set additional user data here if needed
        }
    }

    // Output JavaScript for dynamic session expiration alert
    echo '<script>
        var sessionTimeout = ' . $session_timeout . ';
        var warningThreshold = ' . $warning_threshold . ';

        function checkSessionExpiration() {
            var timeLeft = sessionTimeout - (' . time() . ' - ' . $_SESSION['last_activity'] . ');
            if (timeLeft < warningThreshold) {
                alert("Your session will expire soon. Please save your work or refresh the page.");
            }
        }

        setInterval(checkSessionExpiration, 1000); // Check every second (adjust as needed)
    </script>';
}

?>
