<?php
include('../session/auth.php');
require_once '../session/session_manager.php';
require '../session/db.php';
require '../config/config.php';



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



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';




// Function to generate a random 16-character password
function generateRandomPassword() {
    // Generate 8 bytes of random data
    $randomBytes = random_bytes(8);
    // Convert the random data to a hexadecimal string
    $password = bin2hex($randomBytes);
    return $password;
}


function sendDoctorDetailsEmail($to, $subject, $message) {
    $mail = new PHPMailer;


    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Your SMTP server
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->Username = SMTP_USERNAME; // Use the constant
    $mail->Password = SMTP_PASSWORD; // Use the constant


    $mail->setFrom(SMTP_USERNAME, 'Doctors Account'); // Replace with your email and name
    $mail->addAddress($to);
    $mail->Subject = $subject;
    $mail->Body = $message;



    if (!$mail->send()) {
        echo 'Error: ' . $mail->ErrorInfo;
    }

    
}


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



// Fetch clinics
$clinicQuery = "SELECT clinic_id, clinic_name, address FROM clinic_info";
$clinicResult = $conn->query($clinicQuery);


// Fetch clinics
$doctorQuery = "SELECT doctor_id, First_Name, Last_Name, Specialty  FROM doctors_table";
$doctorResult = $conn->query($doctorQuery);







if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['Add'])) {
        $FirstName = ucwords(strtolower($_POST['FirstName']));
        $LastName = ucwords(strtolower($_POST['LastName']));
        $clinicId = $_POST['clinic'];
        $Specialty = $_POST['specialty'];
        $Fee = $_POST['Fee'];
        $Experience = $_POST['Experience'];
        $patientPhoneNum = $_POST['phone'];
        $Email = $_POST['email'];

        $randomPassword = generateRandomPassword();


          // Hash the password
        $hashedPassword = password_hash($randomPassword, PASSWORD_DEFAULT);

                // Check if the email already exists in the database
        $emailCheckQuery = "SELECT doctor_id FROM doctors_table WHERE Email = ?";
        $emailCheckStmt = $conn->prepare($emailCheckQuery);
        $emailCheckStmt->bind_param("s", $Email);
        $emailCheckStmt->execute();
        $emailCheckStmt->store_result();

        if ($emailCheckStmt->num_rows > 0) {
            // Email already exists, display an error message or handle accordingly
            $_SESSION['errorMessage'] = 'Error: Email already exists in the database.';
            $emailCheckStmt->close();
            header("Location: admin-adddoctor.php");
            exit();
        }

        $emailCheckStmt->close();


        

            // Use the generated password in the SQL query
            $stmt = $conn->prepare("INSERT INTO doctors_table (First_Name, Last_Name, Clinic_ID, Specialty, Experience, Fee, Phone_Number, Email, Password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssisissss", $FirstName, $LastName, $clinicId, $Specialty, $Experience, $Fee, $patientPhoneNum, $Email, $hashedPassword);


            if ($stmt->execute()) {
                // Set the success message
                $_SESSION['successMessage'] = "Doctor added successfully!";
                // Redirect to the same page to avoid form resubmission

                // Send email with doctor details
                $emailSubject = "New Doctor Added";
                $emailMessage = "Dear $FirstName,\n\n";
                $emailMessage .= "A new doctor has been added with the following details:\n";
                $emailMessage .= "Name: $FirstName $LastName\n";
                $emailMessage .= "Email: $Email\n";
                $emailMessage .= "Password: $randomPassword\n\n";
                $emailMessage .= "Thank you for joining!\n\n";

                $emailMessage .= "Please change your password after your first login.\n";

                sendDoctorDetailsEmail($Email, $emailSubject, $emailMessage);

                echo "<script>alert('Email and Password successfully sent.');</script>";

                header("Location: admin-adddoctor.php");
                exit();
            } else {
                // Set an error message if there's an issue
                $errorMessage = "Error: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();



    }



    if (isset($_POST['ConfirmDelete'])) {
        $doctorId = $_POST['doctor'];


         // Debugging output
    echo "Doctor ID: " . $doctorId;

        // Check if a doctor is selected
        if (empty($doctorId) || $doctorId == "none") {
            $errorMessage = "Please select a doctor before confirming deletion.";
        } else {
            // Use prepared statement to prevent SQL injection
            $deleteStmt = $conn->prepare("DELETE FROM doctors_table WHERE doctor_id = ?");
            $deleteStmt->bind_param("i", $doctorId);

            try {
                if ($deleteStmt->execute()) {
                    // Set the delete success message
                    $_SESSION['deleteSuccessMessage'] = "Doctor deleted successfully!";
                    header("Location: admin-adddoctor.php");
                    exit();
                } else {
                    // Set an error message if there's an issue
                    $errorMessage = "Error: " . $deleteStmt->error;
                    $_SESSION['errorMessage'] = "Error: Cannot delete the doctor because of existing appointments.";
                    header("Location: admin-adddoctor.php");
                    exit();
                }
            } catch (mysqli_sql_exception $e) {
                // Catch the foreign key constraint exception
                $errorMessage = "Error: Cannot delete the doctor because of existing appointments.";
                $_SESSION['errorMessage'] = $errorMessage;
                header("Location: admin-adddoctor.php");
                exit();
            
              
            }
            
            // Close the statement
            $deleteStmt->close();
        }
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
    <title>Admin | Add Doctor</title>


    <link rel="icon" href="images/logo.png" type="image/png">

    <link rel="stylesheet" type="text/css" href="style/admin-adddoctor.css">
    <link rel="stylesheet" href="style/transitions.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">



    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
    $(document).ready(function () {
    var clinics = <?php echo json_encode($clinicResult->fetch_all(MYSQLI_ASSOC)); ?>;
    var doctors = <?php echo json_encode($doctorResult->fetch_all(MYSQLI_ASSOC)); ?>;
   

    // Populate Clinic dropdown
    var clinicDropdown = $('#clinic-box');
    clinicDropdown.append('<option value="all">Select a Clinic</option>');
    clinics.forEach(function (clinic) {
        clinicDropdown.append('<option value="' + clinic.clinic_id + '">' + clinic.clinic_name + " - " + clinic.address + '</option>');
    });



    var doctorDropdown = $('#doctor-box');
    doctorDropdown.append('<option value="" hidden>Select a Doctor</option>');
    doctors.forEach(function (doctor) {
        doctorDropdown.append('<option value="' + doctor.doctor_id + '">'+ doctor.doctor_id + " - " + doctor.Last_Name + " , " + doctor.First_Name + " - " + doctor.Specialty + '</option>');
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


                <p>HOME</p>

                <li >
                    <a href="admin-dashboard.php" >
                        <i class='bx bxs-dashboard'></i>
                        <span>Dashboard</span>
                    </a>
                </li>


                
            
                <li>
                    <a href="admin-appointment.php">
                        <i class='bx bxs-time-five' ></i>
                        <span>Appointments</span>
                    </a>
                </li>


                
        

                <li class="active">
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

                <li>
                <a href="error.php">
                    <i class='bx bxs-data'></i>
                    <span>Error Logs</span>
                </a>
                </li>

                <li>
                <a href="eventlogs.php">
                    <i class='bx bxs-data'></i>
                    <span>User Logs</span>
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
           
        
           
        </div>



<div class="first-container">

    <form method="post" action="admin-adddoctor.php" class="add">


        <div class="add-doctor-container">

                <p class="add-doctor-title"> 
                    ADD DOCTOR
                </p>



                <div class="selection-container">

                        
                    <label for="selecteddate">First Name:</label>
                    <div class="selecteddate">
                                <div class="inputbox">
                                    <input type="text" name="FirstName" placeholder="Enter First Name" required="required">
                                </div>
                    </div>


                </div>

                <div class="selection-container">

                        
                    <label for="selecteddate">Last Name:</label>
                    <div class="selecteddate">
                                <div class="inputbox">
                                    <input type="text" name="LastName" placeholder="Enter Last Name" required="required">
                                </div>
                    </div>


                </div>



                <div class="selection-container">

                <label for="clinic-search">Clinic: </label>
                <div class="clinic-search">
                    <select name="clinic" id="clinic-box">
                        <!-- Clinic options will be populated dynamically using JavaScript -->
                    </select>
                </div>
                </div>




                
                <div class="selection-container">

                <label for="doctor-search">Specialty: </label>
                <div class="doctor-search">
                    <select name="specialty" >
                        <option hidden>Select a Specialty</option>
                        <option value="Infant Vaccine">Infant Vaccine</option>
                        <option value="Consultation">Consultation</option>
                        <option value="Mental Health">Mental Health</option>
                        <option value="Family Planning">Family Planning</option>
                        <option value="Psychiatrists">Psychiatrists</option>
                        <option value="Pediatrician">Pediatrician</option>
                        <option value="Animal Vaccine">Animal Vaccine</option>
                     
                    </select>
                </div>



                </div>



      


                <div class="selection-container">

                        
                    <label for="selecteddate">Experience:</label>
                    <div class="selecteddate">
                                <div class="inputbox">
                                    <input name="Experience" type="text" placeholder="How many years experience in the field " required="required">
                                </div>
                    </div>


                </div>


                <div class="selection-container">

                        
                <label for="selecteddate">Consultation Fee:</label>
                <div class="selecteddate">
                            <div class="inputbox">
                                <input name="Fee" type="text" placeholder="Fee " required="required">
                            </div>
                </div>


                </div>


                <div class="selection-container">

                        
                <label for="selecteddate">Phone Number:</label>
                <div class="selecteddate">
                            <div class="inputbox">
                            <input type="tel" id="phone" name="phone" pattern="[+]?[0-9]+[-]?[0-9]+[-]?[0-9]+" placeholder="+639.....">

                            </div>
                </div>


                </div>




                
                <div class="selection-container">

                        
                <label for="selecteddate">Email:</label>
                <div class="selecteddate">
                            <div class="inputbox">
                                <input type="email" id="email" name="email" placeholder="Enter Email" required="required">
                            </div>
                </div>


                </div>




                <div class="button-add">
                        
                <button name="Add" type="submit">Add Doctor</button>



                </div>



 









        </div>

    </form>


    
    <div id="successMessage" class="success-message"><i id="bx-check" class='bx bx-check'></i> </div>

    <div id="errorMessage" class="errorMessage"><i id="bx-error" class='bx bx-x-circle'></i> </div>

    <div id="deleteSuccessMessage" class="deleteSuccessMessage"><i id="bx-error2" class='bx bx-x-circle'></i> </div>

             


    <form method="post" action="admin-adddoctor.php" id="deleteForm">


    <div class="delete-doctor-container">

        <p class="add-doctor-title"> 
                DELETE DOCTOR
            </p>



            <div class="selection-container">

            <label for="doctor-search">Doctor: </label>
            <div class="doctor-search">
                <select name="doctor" id="doctor-box" required="required">
                    <option hidden>Select a Doctor</option>
                    <!-- Doctor options will be populated dynamically using JavaScript -->
                </select>
            </div>



            </div>


            <div class="delete-btn">
                <button id="myBtn" type="button"><i class='bx bxs-trash'></i>Delete Doctor</button>
            </div> 


        </div>



        <div class="delete-modal" id="delete-modal">
                    <div class="delete-modal-content">
                        <p>Are you sure you want to remove this doctor?</p>
                        <div class="modal-buttons">
                            <button id="close-btn" class="close-btn">Close</button>
                            <button type="submit" class="confirm" name="ConfirmDelete">Confirm</button>
                        </div>
                    </div>
                
        </div>

    </form>







       


</div>



 

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




var errorMessage = "<?php echo isset($_SESSION['errorMessage']) ? $_SESSION['errorMessage'] : ''; ?>";
if (errorMessage !== "") {
    var errorMessageDiv = document.getElementById("errorMessage");
    errorMessageDiv.textContent = errorMessage;
    errorMessageDiv.style.display = "block";

    // Scroll to the success message for better visibility
    errorMessageDiv.scrollIntoView({ behavior: 'smooth' });

    // Remove the session variable to avoid displaying the message on subsequent page loads
    <?php unset($_SESSION['errorMessage']); ?>
}


// Check if the deleteSuccess parameter is present in the URL
var deleteSuccessMessage = "<?php echo isset($_SESSION['deleteSuccessMessage']) ? $_SESSION['deleteSuccessMessage'] : ''; ?>";
if (deleteSuccessMessage !== "") {
    var deleteSuccessMessageDiv = document.getElementById("deleteSuccessMessage");
    deleteSuccessMessageDiv.textContent = deleteSuccessMessage;
    deleteSuccessMessageDiv.style.display = "block";

    // Scroll to the delete success message for better visibility
    deleteSuccessMessageDiv.scrollIntoView({ behavior: 'smooth' });

    // Remove the session variable to avoid displaying the message on subsequent page loads
    <?php unset($_SESSION['deleteSuccessMessage']); ?>
}









    </script>


<script>
// Get the modal
var modal = document.getElementById("delete-modal");

// Get the <span> element that closes the modal
var span = document.getElementById("close-btn");


var deleteBtn = document.getElementById("myBtn");
deleteBtn.onclick = function() {
    modal.style.display = "block";
    return false; // Prevent default form submission
}


// When the user clicks on "Close", close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks on "Confirm", submit the delete form
var confirmBtn = document.querySelector(".confirm");
confirmBtn.onclick = function() {
    document.getElementById("deleteForm").submit();
}


document.getElementById("deleteForm").addEventListener("submit", function (event) {
    var selectedDoctor = document.getElementById("doctor-box").value;

    // Check if the "Confirm" button is clicked and a doctor is not selected
    if (event.submitter && event.submitter.classList.contains("confirm")) {
        console.log("Selected Doctor:", selectedDoctor);

        // Check if the selectedDoctor is the default value
        if (selectedDoctor === "Select a Doctor") {
            alert("Please select a doctor before confirming deletion.");
            event.preventDefault();  // Prevent the form submission
        }
    }
});




// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}
</script>







 
</body>
</html>
