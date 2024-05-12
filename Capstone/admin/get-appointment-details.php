<?php
// Include necessary files and initialize the session
include('../session/auth.php');
require_once '../session/session_manager.php';
require '../session/db.php';

// Ensure the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the appointment ID from the POST data
    $appointmentId = $_POST['appointment_id'];

    // Validate and sanitize the appointment ID if needed

    // Fetch the appointment details from the database based on the ID
    $appointmentDetails = getAppointmentDetails($appointmentId, $conn);

    // Output the appointment details as a JSON response
    header('Content-Type: application/json');
    echo json_encode($appointmentDetails);
} else {
    // Handle other types of requests or return an error response
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Invalid request']);
}

// Function to fetch appointment details based on the ID
function getAppointmentDetails($appointmentId, $conn) {
    // Sanitize and validate the appointment ID
    $appointmentId = mysqli_real_escape_string($conn, $appointmentId);

    // Query to fetch appointment details based on the ID
    $query = "SELECT * FROM appointments WHERE Appointment_ID = $appointmentId";

    // Execute the query
    $result = $conn->query($query);

    // Check if the query was successful
    if ($result === false) {
        // Return an error response if the query fails
        return ['error' => 'Error executing the query: ' . $conn->error];
    }

    // Check if a row was returned
    if ($result->num_rows > 0) {
        // Fetch the appointment details
        $appointmentDetails = $result->fetch_assoc();

        // Return the details as an associative array
        return $appointmentDetails;
    } else {
        // Return an error response if the appointment ID is not found
        return ['error' => 'Appointment not found'];
    }
}
?>
