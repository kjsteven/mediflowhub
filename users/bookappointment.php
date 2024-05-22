<?php

require '../session/db.php';
require_once '../session/session_manager.php';
require '../admin/EventLogger.php'; 



$eventLogger = new EventLogger();
$eventLogger->logAppointmentEvent($userId, $date, $timeSlot, $doctorId, $patientId);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assuming you have a session variable storing the user ID
session_start();
if (!isset($_SESSION['user_id'])) {
    // Redirect to login or handle unauthorized access
    header("Location: login.php");
    exit();
}

// Fetch clinics
$clinicQuery = "SELECT clinic_id, clinic_name, address FROM clinic_info";
$clinicResult = $conn->query($clinicQuery);

// Fetch specialties
$specialtyQuery = "SELECT DISTINCT specialty FROM doctors_table";
$specialtyResult = $conn->query($specialtyQuery);

// Fetch doctors
$doctorQuery = "SELECT doctor_id, CONCAT(First_Name, ' ', Last_Name) AS doctor_name, clinic_id, specialty FROM doctors_table";
$doctorResult = $conn->query($doctorQuery);

// Fetch patients
// Fetch patients
$userId = $_SESSION['user_id'];  // Get the user ID from the session
$patientQuery = "SELECT Patient_id, CONCAT(First_Name, ' ', Last_Name) AS patient_name, Date_of_Birth 
                FROM patients_table
                WHERE user_id = ?";
$patientStmt = $conn->prepare($patientQuery);
$patientStmt->bind_param("s", $userId);
$patientStmt->execute();
$patientResult = $patientStmt->get_result();
$patientStmt->close();


// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the 'timeSlot' key exists in the $_POST array
    if (isset($_POST['timeSlot'])) {
        // Retrieve form data
        $date = $_POST['selecteddate'];
        $timeSlot = $_POST['timeSlot'];
        $diagnosis = $_POST['diagnosis'];
     
        $patientPhoneNum = $_POST['phone'];
        $doctorId = $_POST['doctor'];

        // Check if the 'patient' key exists in the $_POST array
        $patientId = isset($_POST['patient']) ? $_POST['patient'] : null;

        $clinicId = $_POST['clinic'];

        // Get the user ID from the session
        $userId = $_SESSION['user_id'];

        // Check if any required field is empty
        if (empty($date) || empty($timeSlot) || empty($patientPhoneNum) || empty($doctorId) || empty($patientId) || empty($clinicId) || empty($userId)) {
            // Set an error message for empty fields
            $errorMessage = "Please fill in all required fields.";
        } else {
            // Prepare and execute the SQL query to insert data into the appointment table
            $stmt = $conn->prepare("INSERT INTO appointments (Date, time_slot, Patient_Phone_Num,Diagnosis , doctor_id, Patient_id, user_id, Status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')");

            // Bind parameters
            $stmt->bind_param("sssssss", $date, $timeSlot, $patientPhoneNum,  $diagnosis, $doctorId, $patientId, $userId);

            // Execute the statement
            if ($stmt->execute()) {

                // Set the success message
                $_SESSION['successMessage'] = "Appointment added successfully!";
                // Redirect to the same page to avoid form resubmission
                header("Location: bookappointment.php");
                exit();
            } else {
                // Set an error message if there's an issue
                $errorMessage = "Error: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        }
    } else {
        echo "Error: 'timeSlot' is not set in the form submission.";
    }
}

// Close the database connection
$conn->close();

?>










<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>


    <link rel="icon" href="images/logo.png" type="image/png">

    <link rel="stylesheet" type="text/css" href="style/bookappointment.css">
    <link rel="stylesheet" href="style/transitions.css">
    

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">


    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
    $(document).ready(function () {
    var clinics = <?php echo json_encode($clinicResult->fetch_all(MYSQLI_ASSOC)); ?>;
    var specialties = <?php echo json_encode($specialtyResult->fetch_all(MYSQLI_ASSOC)); ?>;
    var doctors = <?php echo json_encode($doctorResult->fetch_all(MYSQLI_ASSOC)); ?>;
    var patients = <?php echo json_encode($patientResult->fetch_all(MYSQLI_ASSOC)); ?>;

    // Populate Clinic dropdown
    var clinicDropdown = $('#clinic-box');
    clinicDropdown.append('<option value="all">Select a Clinic</option>');
    clinics.forEach(function (clinic) {
        clinicDropdown.append('<option value="' + clinic.clinic_id + '">' + clinic.clinic_name + " - " + clinic.address + '</option>');
    });

    // Populate Specialty dropdown
    var specialtyDropdown = $('#specialty-box');
    specialtyDropdown.append('<option value="all">Select a Service</option>');
    specialties.forEach(function (specialty) {
        specialtyDropdown.append('<option value="' + specialty.specialty + '">' + specialty.specialty + '</option>');
    });

    // Update Doctor dropdown based on Clinic and Specialty selection
    $('#clinic-box, #specialty-box').on('change', function () {
        var selectedClinic = $('#clinic-box').val();
        var selectedSpecialty = $('#specialty-box').val();

        var filteredDoctors = doctors.filter(function (doctor) {
            return (selectedClinic == 'all' || doctor.clinic_id == selectedClinic) &&
                (selectedSpecialty == 'all' || doctor.specialty == selectedSpecialty);
        });

        var doctorDropdown = $('#doctor-box');
        doctorDropdown.empty();

        if (filteredDoctors.length > 0) {
            // If there are available doctors, populate the dropdown
            doctorDropdown.append('<option hidden>Select a Doctor</option>');
            filteredDoctors.forEach(function (doctor) {
                doctorDropdown.append('<option value="' + doctor.doctor_id + '">' + doctor.doctor_name + '</option>');
            });
        } else {
            // If no doctors are available, display a message
            doctorDropdown.append('<option hidden>No doctors available </option>');
            doctorDropdown.append('<option value="" disabled>No doctors available</option>');
        }
    });

    // Populate Patient dropdown
    var patientDropdown = $('#patient-box');
    patientDropdown.append('<option hidden>Select a Patient or Search...</option>');
    patients.forEach(function (patient) {
        patientDropdown.append('<option value="' + patient.Patient_id + '">' + patient.patient_name + " - (" + patient.Date_of_Birth + ")" + '</option>');
    });
});
</script>




 
    
</head>
<body>

    <div id="sidebar" class="sidebar">
        <div class="logo">
            <img src="images/MediFlowHub.png" alt="">

            <i class='bx bx-x' id="close-sidebar"></i>
        </div>
            <ul class="menu">

                <li>
                    <a href="dashboard.php" >
                        <i class='bx bxs-dashboard'></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                
            
                <li class="active">
                    <button class="dropdown-btn">
                        <i class='bx bxs-time-five'></i>
                        <span>Appointments</span>
                        <i class='bx bxs-chevron-down'></i>
                    </button>

                    <div class="dropdown-container">
                            <a href="appointments.php">View Appointment</a>
                            <a href="bookappointment.php">Book Appointment</a>

                    </div>

                </li>


                
                <li>
                    <a href="availabledoctors.php">
                        <i class='bx bxs-user-rectangle' ></i>
                        <span>Doctors</span>
                    </a>
                </li>

              

                <li>
                    <a href="locations.php">
                    <i class='bx bxs-map'></i>
                        <span>Locations</span>
                    </a>
                </li>

                <li>
                    <a href="notifications.php">
                        <i class='bx bxs-bell' ></i>
                        <span>Notifications</span>
                    </a>
                </li>

                <li>
                    <a href="Profile.php">
                        <i class='bx bxs-cog' ></i>
                        <span>Settings</span>
                    </a>
                </li>


                <li class="logout">
                    <a href="logout.php" id="logout-link">
                        <i class='bx bx-log-out'></i>
                        <span>Logout</span>
                    </a>
                </li>






            </ul>
    
    </div>



    <div class="main--content">


        <div class="header--wrapper" id="lessopacity">
         
         <div class="menu-search">
         
                     <i class='bx bx-menu' id="menu-toggle"></i>

                 
         </div>
        
        
        
        </div>

        
        <h1 id="h1" >Book Appointment</h1>



    <form method="post" action="bookappointment.php" id="appointmentForm">


       
        

        <div class="inputboxes" id="inputboxes">



                    <div class="selection-container">

                        <label for="clinic-search">Clinic: </label>
                        <div class="clinic-search">
                            <select name="clinic" id="clinic-box">
                                <!-- Clinic options will be populated dynamically using JavaScript -->
                            </select>
                        </div>
                    </div>


                    <div class="selection-container">

                        
                        <label for="specialty-search">Choose a Service: </label>
                        <div class="specialty-search">
                            <select name="specialty" id="specialty-box">
                                <!-- Specialty options will be populated dynamically using JavaScript -->
                            </select>
                        </div>


                    </div>


                    <div class="selection-container">

                        <label for="doctor-search">Doctor: </label>
                        <div class="doctor-search">
                            <select name="doctor" id="doctor-box">
                                <option hidden>Select a Doctor</option>
                                <!-- Doctor options will be populated dynamically using JavaScript -->
                            </select>
                        </div>


                    
                    </div>


                    <div class="selection-container">
                        <label for="selecteddate">Choose a Date:</label>
                        <div class="selecteddate">
                            <div class="inputbox">
                                <input type="date" name="selecteddate" id="selecteddate" placeholder="Select a date" required="required" onchange="updateSelectedDate()">
                            </div>
                        </div>
                    </div>



                       

                       



                       
        </div>


  




        <div  id="timeslots"  class="timeslots">
                    <?php
                    // Set the start and end times
                    $start_time = strtotime('09:00 AM');
                    $end_time = strtotime('09:00 PM');

                    // Set the fixed duration for each timeslot (in seconds)
                    $duration = 5400; // 1.5 hours (5400 seconds)

                    // Set the interval to 2 hours (7200 seconds)
                    $interval = 7200;

                    // Initialize the current time variable with the start time
                    $current_time = $start_time;

                    while ($current_time + $duration <= $end_time) {
                        // Format the start and end times of the current timeslot as readable strings
                        $start_time_formatted = date('h:i A', $current_time);
                        $end_time_formatted = date('h:i A', $current_time + $duration);

                        // Output the timeslot button
                        echo '<div class="timeslot-container">';
                        echo '<button type="button" class="timeslot-button">' . $start_time_formatted . ' - ' . $end_time_formatted . '</button>';
                        echo '<button type="button" id="booknow" class="booknow-button" onclick="showFormContainer(\'' . $start_time_formatted . ' - ' . $end_time_formatted . '\')">Book Now</button>';

                        echo '</div>';
                    
              
                
                        // Increment the current time by the interval
                        $current_time += $interval;
                    }
        ?>


        </div>




<div id="formcontainer" class="modal-container">


        
        <div  class="formcontainer">



                                <div class="paragraph">
                                    <p>Booking for Slot: </p>
                                </div>

                                <div class="DateInfo">
                                    <p>Date: </p>
                                </div>

                                <div class="ClinicInfo">
                                    <p>Clinic: </p>
                                </div>




                                <div class="DoctorInfo">
                                    <p>Doctor Info: </p>
                                </div>


                                <div class="br2"></div>




                                <div class="paragraph1">
                                    <p>Please confirm that you would like to request the following appointment.</p>
                                </div>


                                <div class="CurrentPatientsBox" id="Current_Patient">
                                        <h2>Select a patient: </h2>
                                        <div class="patient-search">
                                            <select name="patient" id="patient-box">
                                                <!-- Patient options will be populated dynamically using JavaScript -->
                                            </select>
                                        </div>
                                    </div>


                                <h2>Diagnosis </h2>
                                <div class="paragraph1">
                                    <p>Input your diagnosis here</p>
                                </div>

                                <div class="input-box">
                                    <input type="text"  name="diagnosis" placeholder="Diagnosis....">

                                </div>


                                    


                                <h2>Phone No. </h2>
                                <div class="paragraph1">
                                    <p>Input your phone number below</p>
                                </div>

                                <div class="input-box">
                                    <input type="tel" id="phone" name="phone" pattern="[+]?[0-9]+[-]?[0-9]+[-]?[0-9]+" placeholder="+639.....">

                                </div>

                                <div class="bottom-buttons">
                                    <button  id="generatePdfButton" type="submit" class="Req-Btn" name="submit">Request Appointment</button>
                                    <button onclick="myFunction()" class="Cancel-Btn" id="close-btn">Close</button>
                                </div>

        </div>








        </div>





    </form>




<div id="successMessage" class="success-message"><i class='bx bx-check'></i> Appointment booked successfully!</div>
<div id="errorMessage" class="error-message"><i class='bx bxs-x-circle'></i> </div>


<a href="user-addpatient.php" class="addpatientbtn">Add Patient</a>



</div>





<script>
        document.getElementById('selecteddate').min = new Date().toISOString().split('T')[0];
        
        function updateSelectedDate() {
            // Additional logic when the date changes
            var selectedDate = document.getElementById('selecteddate').value;
            console.log('Selected Date:', selectedDate);

            // Add your custom logic here
            
            // Example: Display an alert if the selected date is in the past
            var currentDate = new Date().toISOString().split('T')[0];
            if (selectedDate < currentDate) {
                alert('Please select a future date.');
                document.getElementById('selecteddate').value = currentDate;
            }
        }
    </script>





<!-- Add this script in your HTML file -->
<script>
    

                    /* Loop through all dropdown buttons to toggle between hiding and showing its dropdown content - This allows the user to have multiple dropdowns without any conflict */
                    var dropdown = document.getElementsByClassName("dropdown-btn");
                    var i;

                    for (i = 0; i < dropdown.length; i++) {
                    dropdown[i].addEventListener("click", function() {
                    this.classList.toggle("active");
                    var dropdownContent = this.nextElementSibling;
                    if (dropdownContent.style.display === "block") {
                    dropdownContent.style.display = "none";
                    } else {
                    dropdownContent.style.display = "block";
                    }
                    });
                    }

</script>


<script>
    function showFormContainer(timeSlot) {
        // Set the selected time slot in the form
        document.querySelector('.formcontainer .paragraph p').innerHTML = 'Booking for Slot: ' + timeSlot;

         // Add a hidden input field to store the selected time slot
         var hiddenInput = document.createElement("input");
        hiddenInput.type = "hidden";
        hiddenInput.name = "timeSlot";
        hiddenInput.value = timeSlot;
        document.getElementById("appointmentForm").appendChild(hiddenInput);

        // Get the selected date, clinic, and doctor
        var selectedDate = document.getElementById('selecteddate').value;
        var selectedClinic = document.getElementById('clinic-box').options[document.getElementById('clinic-box').selectedIndex].text;
        var selectedDoctor = document.getElementById('doctor-box').options[document.getElementById('doctor-box').selectedIndex].text;
        var selectedSpecialty = document.getElementById('specialty-box').options[document.getElementById('specialty-box').selectedIndex].text;
        // Display the selected date, clinic, and doctor in the form container
        document.querySelector('.formcontainer .DateInfo p').innerHTML = 'Date: ' + selectedDate;
        document.querySelector('.formcontainer .ClinicInfo p').innerHTML = 'Clinic: ' + selectedClinic;
        document.querySelector('.formcontainer .DoctorInfo p').innerHTML = 'Doctor Info: ' + selectedDoctor + " - " + selectedSpecialty;

        // Show the form container with smooth transition
        var formContainer = document.getElementById('formcontainer');
        formContainer.style.display = 'block';
        setTimeout(() => {
            formContainer.style.opacity = '1';
          
           
        }, 10);

       


        
    }

   function myFunction() {
    // Reset the form fields to their default or empty state
    document.getElementById('clinic-box').value = 'all';
    document.getElementById('specialty-box').value = 'all';
    document.getElementById('doctor-box').innerHTML = '<option hidden>Select a Doctor</option>';
    document.getElementById('selecteddate').value = '';
    document.getElementById('patient-box').value = '';

    document.getElementById('phone').value = '';

    // Close the form container with smooth transition
    var formContainer = document.getElementById('formcontainer');
    formContainer.style.opacity = '0';
    setTimeout(() => {
        formContainer.style.display = 'none';
    }, 500);

    // Restore opacity for other elements
    setElementOpacity('lessopacity', '1');
    setElementOpacity('inputboxes', '1');
    setElementOpacity('h1', '1');
    setElementOpacity('timeslots', '1');
}


    function setElementOpacity(elementId, opacityValue) {
        var element = document.getElementById(elementId);
        element.style.opacity = opacityValue;
    }



      // Check if the success parameter is present in the URL
    var successMessage = "<?php echo isset($_SESSION['successMessage']) ? $_SESSION['successMessage'] : ''; ?>";
if (successMessage !== "") {
    var successMessageDiv = document.getElementById("successMessage");
    successMessageDiv.textContent = successMessage;
    successMessageDiv.style.display = "block";

    // Scroll to the success message for better visibility
    successMessageDiv.scrollIntoView({ behavior: 'smooth' });

    // Remove the session variable to avoid displaying the message on subsequent page loads
    <?php unset($_SESSION['successMessage']); ?>
}


var errorMessage = "<?php echo isset($errorMessage) ? $errorMessage : ''; ?>";
if (errorMessage !== "") {
    var errorMessageDiv = document.getElementById("errorMessage");
    errorMessageDiv.textContent = errorMessage;
    errorMessageDiv.style.display = "block";

    // Scroll to the error message for better visibility
    errorMessageDiv.scrollIntoView({ behavior: 'smooth' });
}










</script>












<script src="script/script.js"></script>








</body>




</html>
