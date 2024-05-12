<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


include('../session/auth.php');
require_once '../session/session_manager.php';
require '../session/db.php';
require '../config/config.php';
require '../vendor/autoload.php';


start_secure_session(); 


if (!isset($_SESSION["username"])) {
    header("Location: ../users/login.php"); 
    exit;
}

if (!check_admin_role()) {
    // Redirect to the user dashboard or show an error message
    header('Location: ../users/dashboard.php');
    exit();
}



// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}






$query = "SELECT a.Appointment_ID, p.Last_Name AS Patient_Last_Name, p.First_Name AS Patient_First_Name, 
                 d.Last_Name AS Doctor_Last_Name, d.First_Name AS Doctor_First_Name, 
                 d.Clinic_ID AS Clinic_ID, a.time_slot, a.Date, a.Status,
                 c.Clinic_Name, c.Address, d.Specialty, a.Diagnosis
                 
          FROM appointments a
          JOIN patients_table p ON a.Patient_id = p.Patient_id
          JOIN doctors_table d ON a.doctor_id = d.doctor_id
          JOIN clinic_info c ON d.Clinic_ID = c.Clinic_ID";
     


$result = $conn->query($query);

if (isset($_GET['confirm_appointment']) && !empty($_GET['confirm_appointment'])) {
    $appointmentId = $_GET['confirm_appointment'];

    // Update the appointment status to 'Confirmed' in the database
    $updateQuery = "UPDATE appointments SET Status = 'Confirmed' WHERE Appointment_ID = $appointmentId";
    $conn->query($updateQuery);

    // Retrieve user and doctor emails
    $emailQuery = "SELECT p.Email AS Patient_Email, d.Email AS Doctor_Email
                   FROM appointments a
                   JOIN users p ON a.user_id = p.user_id
                   JOIN doctors_table d ON a.doctor_id = d.doctor_id
                   WHERE a.Appointment_ID = $appointmentId";

    $emailResult = $conn->query($emailQuery);

    if ($emailResult && $emailResult->num_rows > 0) {
        $emailData = $emailResult->fetch_assoc();
        $patientEmail = $emailData['Patient_Email'];
        $doctorEmail = $emailData['Doctor_Email'];

        // Store debug information in $_SESSION['successMessage']
        $_SESSION['successMessage'] = "Patient Email: $patientEmail | Doctor Email: $doctorEmail";

        // Send confirmation emails
        sendConfirmationEmail($patientEmail, 'Patient');
        sendConfirmationEmail($doctorEmail, 'Doctor');
    }

    // Redirect to the page after processing
    header("Location: admin-appointment.php");
    exit();
}



function sendConfirmationEmail($recipientEmail, $recipientType) {
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->Username = SMTP_USERNAME; 
    $mail->Password = SMTP_PASSWORD; 

    $mail->setFrom(SMTP_USERNAME, 'MediflowHub | Appointment Confirmation');
    $mail->addAddress($recipientEmail);

    $mail->isHTML(true);
    $mail->Subject = 'Appointment Confirmation';

    if ($recipientType === 'Patient') {
        $mail->Body = 'Your appointment has been confirmed. Thank you!';
    } elseif ($recipientType === 'Doctor') {
        $mail->Body = 'Your appointment has been confirmed. Please be prepared.';
    }

     if (!$mail->send()) {
         $_SESSION['errorMessage'] = 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
     } 
 }
?>




<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | List of Appointments</title>


    <link rel="icon" href="images/logo.png" type="image/png">

    <link rel="stylesheet" type="text/css" href="style/admin-appointment.css">
    <link rel="stylesheet" href="style/transitions.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">




    <!-- Include jQuery library -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>

<!-- Include DataTables JS -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<!-- Include DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">



<script defer>
    $(document).ready(function () {
        // Initialize DataTable with additional options
        $('#myTable').DataTable({
            "lengthMenu": [10, 25, 50, 75, 100],
            "pageLength": 10,
            "pagingType": "full_numbers",
            "language": {
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoEmpty": "Showing 0 to 0 of 0 entries",
                "infoFiltered": "(filtered from _MAX_ total entries)",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                }
            }
        });
        $('.view-button').on('click', function (e) {
        e.preventDefault();

        // Get the appointment ID from the button's data attribute
        var appointmentId = $(this).data('appointment-id');

        // Make an AJAX request to get the appointment details
        $.ajax({
            type: 'POST',
            url: 'get-appointment-details.php',
            data: { appointment_id: appointmentId },
            dataType: 'json',
            success: function (response) {
                if (response.error) {
                    // Handle the error, for example, show an alert
                    alert('Error: ' + response.error);
                } else {
                    // Display the appointment details in an alert box
                    showAlert('Appointment Details', formatDetails(response));
                }
            },
            error: function () {
                // Handle AJAX error, for example, show an alert
                alert('Error fetching appointment details');
            }
        });
    });
});

// Function to format appointment details
function formatDetails(details) {
    var formattedDetails = '';
    for (var key in details) {
        formattedDetails += key + ': ' + details[key] + '\n';
    }
    return formattedDetails;
}

// Function to show an alert box with specified title and content
function showAlert(title, content) {
    alert(content);
}
    /* Other scripts and functions */
</script>






 
    
</head>
<body>

    <div id="sidebar" class="sidebar">
        <div class="logo">
            <img src="images/MediFlowHub.png" alt="">

            <i class='bx bx-x' id="close-sidebar"></i>
        </div>

        
            <ul class="menu">


                <p>HOME</p>

                <li >
                    <a href="admin-dashboard.php" >
                        <i class='bx bxs-dashboard'></i>
                        <span>Dashboard</span>
                    </a>
                </li>


                
            
                <li class="active">
                    <a href="admin-appointment.php">
                        <i class='bx bxs-time-five' ></i>
                        <span>Appointments</span>
                    </a>
                </li>


                
        

                <li>
                <button class="dropdown-btn">
                        <i class='bx bxs-user-rectangle' ></i>
                        <span>Doctors</span>
                        <i class='bx bxs-chevron-down'></i>
                    </button>

                    <div class="dropdown-container">
                            <a href="admin-adddoctor.php">Add/Delete Doctor</a>
                            <a href="admin-viewalldoctor.php">View All Doctor</a>
                          
                    </div>

                </li>


                <li>
                    <a href="admin-viewallpatient.php">
                        <i class='bx bx-plus-medical' ></i>
                        <span>Patients</span>
                    </a>
                </li>


                <p>DATABASE</p>


                <li>
                    <a href="admin-viewallpatient.php">
                    <i class='bx bxs-data'></i>
                        <span>Backup</span>
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

        <div class="header--wrapper">


        
         
            <div class="menu-search">


            
                        <i class='bx bx-menu' id="menu-toggle"></i>

                       

            </div>



            <div class="navigation">

                <p>ADMIN <span>/ LIST OF APPOINTMENTS</span></p>
              


            </div>

          



           
   
       

        </div>



        <div class="inside-container">
            <div class="rectangle">
            <table id="myTable" class="display">

                <thead id="thead" >
                    <tr>
                        <th>Appointment No.</th>
                        <th>Patient Name</th>
                        <th>Doctor Name</th>
                        <th>Clinic</th>
                        <th>Appointment Time</th>
                        <th>Appointment Date</th>
                        <th>Diagnosis</th>
                        <th>Chosen Service</th>
                       
                        
                        <th>Status</th>
                        <th>Action</th>
                        
                    <!-- <th>View</th> -->
                    </tr>

                </thead>

                <tbody>

                            <?php
                 
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>{$row['Appointment_ID']}</td>";

                                echo "<td>{$row['Patient_Last_Name']}, {$row['Patient_First_Name']}</td>";
                                echo "<td>{$row['Doctor_Last_Name']}, {$row['Doctor_First_Name']}</td>";

                         
                                $clinicId = $row['Clinic_ID'];
                                $clinicName = getClinicName($clinicId, $conn);
                                echo "<td>{$clinicName}</td>";

                            
                                echo "<td>{$row['time_slot']}</td>";

                                $dateString = $row['Date'];

                         
                                $dateTime = new DateTime($dateString);

                                $formattedDate = $dateTime->format('F j, Y');

                                echo "<td>{$formattedDate}</td>";


                                echo "<td>{$row['Diagnosis']}</td>";
                                echo "<td>{$row['Specialty']}</td>";
                                

                                $status = $row['Status'];
                                $statusClass = '';
                            
                                switch ($status) {
                                    case 'Pending':
                                        $statusClass = 'c-pill c-pill--warning'; 
                                        break;
                                    case 'Confirmed':
                                        $statusClass = 'c-pill c-pill--success'; 
                                        break;

                                        case 'Completed':
                                            $statusClass = 'c-pill c-pill--success'; 
                                            break;
                                    case 'Cancelled':
                                        $statusClass = 'c-pill c-pill--danger'; 
                                        break;
                        
                            
                                    default:
                            
                                        $statusClass = 'default-status';
                                        break;
                                }
                     
                                echo "<td><p class='{$statusClass}'>{$status}</p></td>";

                                echo "<td class='button-action'>";

                                if ($status === 'Cancelled') {
                                    // Show only View Button for Cancelled status
                                    echo "<button class='view-button' data-appointment-id='{$row['Appointment_ID']}'>View <i class='bx bx-show'></i></button>";
                                } elseif ($status === 'Pending') {
                                    // Show Confirm, Cancel, and View Buttons for Pending status
                                    echo "<form action='' method='get'>
                                            <input type='hidden' name='confirm_appointment' value='{$row['Appointment_ID']}'>
                                            <button type='submit' class='confirm-button'>Confirm <i class='bx bxs-show'></i></button>
                                          </form>";
                                    echo "<a href='cancel-reason.php?appointment_id={$row['Appointment_ID']}' class='cancel-button'>Cancel <i class='bx bxs-message-square-edit'></i></a>";
                                    echo "<button class='view-button' data-appointment-id='{$row['Appointment_ID']}'>View <i class='bx bx-show'></i></button>";
                                } elseif ($status === 'Confirmed' || $status === 'Completed') {
                                    // Show only View Button for Confirmed or Completed status
                                    echo "<button class='view-button' data-appointment-id='{$row['Appointment_ID']}'>View <i class='bx bx-show'></i></button>";
                                }
                                
                                echo "</td>";
                                
                            
                            

                                //
                                echo "</tr>";
                            }



                            function getClinicName($clinicId, $conn) {
                                // Sanitize Clinic_ID to prevent SQL injection
                                $clinicId = mysqli_real_escape_string($conn, $clinicId);
                            
                                // Query to fetch Clinic_Name based on Clinic_ID
                                $query = "SELECT Clinic_Name FROM clinic_info WHERE Clinic_ID = $clinicId";
                                $result = $conn->query($query);
                            
                                // Check if the query was successful
                                if ($result === false) {
                                    die("Error executing the query: " . $conn->error);
                                }
                            
                                // Check if a row was returned
                                if ($result->num_rows > 0) {
                                    $row = $result->fetch_assoc();
                                    return $row['Clinic_Name'];
                                } else {
                                    // Return an empty string or a default value if Clinic_ID is not found
                                    return "Clinic Not Found";
                                }
                            }


                        
                            ?>


                </tbody>

            </table>
        </div>


      

        </div>
                   
<div id="successMessage" class="success-message"><i class='bx bx-check'></i></div>

<div id="errorMessage" class="error-message"><i class='bx bxs-x-circle'></i> </div>


        
</div>

      


        


    <script src="script/script.js"></script>


    
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
    var successMessage = "<?php echo isset($_SESSION['successMessage']) ? $_SESSION['successMessage'] : ''; ?>";
    if (successMessage !== "") {
        var successMessageDiv = document.getElementById("successMessage");
        successMessageDiv.textContent = successMessage;
        successMessageDiv.style.display = "block";

        // Scroll to the success message for better visibility
        successMessageDiv.scrollIntoView({
            behavior: 'smooth'
        });

        // Remove the session variable to avoid displaying the message on subsequent page loads
        <?php unset($_SESSION['successMessage']); ?>;
    }

    var errorMessage = "<?php echo isset($_SESSION['errorMessage']) ? $_SESSION['errorMessage'] : ''; ?>";

    if (errorMessage !== "") {
        var errorMessageDiv = document.getElementById("errorMessage");
        errorMessageDiv.textContent = errorMessage;
        errorMessageDiv.style.display = "block";

        // Scroll to the error message for better visibility
        errorMessageDiv.scrollIntoView({
            behavior: 'smooth'
        });

        <?php unset($_SESSION['errorMessage']); ?>;
    }
</script>





 
</body>
</html>