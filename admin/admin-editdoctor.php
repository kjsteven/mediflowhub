<?php
include('../session/auth.php');
require_once '../session/session_manager.php';
require '../session/db.php';


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



// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve updated patient information from the form
    $doctor_id = $_GET['doctor_id'];
    $first_name = $_POST['FirstName'];
    $last_name = $_POST['LastName'];
    $email = $_POST['Email'];
    $Specialty = $_POST['Specialty'];
    $Phone = $_POST['Phone_Number'];
    

    // Update doctor information in the database
    $query = "UPDATE doctors_table SET First_Name=?, Last_Name=?, Email=?, Specialty=?, Phone_Number=? WHERE doctor_id=?";
    $statement = $conn->prepare($query);
    $statement->bind_param('sssssi', $first_name, $last_name,  $email,  $Specialty,  $Phone,  $doctor_id);
    $result = $statement->execute();

    if ($result) {
        $_SESSION['successMessage'] = "Doctor information updated successfully";
    } else {
        $_SESSION['errorMessage'] = "Error updating doctor information";
    }

    // Close the statement
    $statement->close();
    
    // Redirect to the same page to prevent form resubmission
    header("Location: {$_SERVER['PHP_SELF']}?doctor_id=$doctor_id");
    exit();
}

// Check if a patient ID is provided in the URL
if (isset($_GET['doctor_id'])) {
    $doctor_id = $_GET['doctor_id'];

    // Retrieve patient information from the database based on the provided ID
    $query = "SELECT * FROM doctors_table WHERE doctor_id = ?";
    $statement = $conn->prepare($query);
    $statement->bind_param('i', $doctor_id);
    $statement->execute();

    // Get the result
    $result = $statement->get_result();

    // Fetch the patient's information
    $doctor = $result->fetch_assoc();

    // Close the statement
    $statement->close();
} else {
    // Handle the case where no patient ID is provided
    // You may redirect the user to a list of patients or show an error message
    header("Location: doctor-viewalldoctor.php");
    exit();
}
?>




<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Edit Doctor</title>


    <link rel="icon" href="images/logo.png" type="image/png">

    <link rel="stylesheet" type="text/css" href="style/admin-editpatient.css">
    <link rel="stylesheet" href="style/transitions.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">



 
    
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

             



                            <div class="inside-container">


                                    <div class="title-1">
                                                <p>Edit doctor</p>

                                    </div>

                                    <div id="successMessage" class="success-message"><i id="bx-check" class='bx bx-check'></i> </div>

                                    <div id="errorMessage" class="errorMessage"><i id="bx-error" class='bx bx-x-circle'></i> </div>


                                    <form action="<?php echo $_SERVER['PHP_SELF'] . "?doctor_id=$doctor_id"; ?>" method="POST">

                                                <div class="input-container">

                                    
                                                    <label for="FirstName">First Name<span>*</span></label>
                                            
                                                            
                                                    <input type="text" name="FirstName" placeholder="Enter First Name" required="required" value="<?php echo htmlspecialchars($doctor['First_Name']); ?>">
                                                        
                                            

                                                </div>


                                                <div class="input-container">

                                                                                    
                                                <label for="FirstName">Last Name<span>*</span></label>

                                                        
                                                <input type="text" name="LastName" placeholder="Enter Last Name" required="required" value="<?php echo htmlspecialchars($doctor['Last_Name']); ?>">
                                                    


                                                </div>


                                                
                                                <div class="input-container">

                                                                                                                                            
                                                    <label for="Email">Email:</label>

                                                            
                                                    <input type="text" name="Email"  placeholder="Email" required="required" value="<?php echo htmlspecialchars($doctor['Email']); ?>">
                                                        


                                                </div>





                                                
                                              


                                                <div class="input-container">

                                                                                                                                            
                                                    <label for="Specialty">Specialty:</label>

                                                            
                                                    <input type="text" name="Specialty"  placeholder="Specialty" required="required" value="<?php echo htmlspecialchars($doctor['Specialty']); ?>">
                                                        


                                                </div>


                                                
                                                <div class="input-container">

                                                                                                                                            
                                                    <label for="Phone_Number">Phone:</label>

                                                            
                                                    <input type="tel" name="Phone_Number"  required="required" value="<?php echo htmlspecialchars($doctor['Phone_Number']); ?>">
                                                        


                                                </div>



                                            

                                                <button type="submit" class="savebtn">Save</button>





    
                                        



                                    
                        
                                        
                                    </form>



                               
                              


                                    
       




                                </div>


                                
                                <div class="backbtn">
                                              
                                              <a href="admin-viewalldoctor.php">Back to View All Doctor</a>
                                             

                                </div>





                  




                                </div>

















                            </div>
                            
                            




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


</script>



<!-- Add the following JavaScript code at the end of your HTML file -->
<script>
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
        // Scroll to the error message for better visibility
        errorMessageDiv.scrollIntoView({ behavior: 'smooth' });
        // Remove the session variable to avoid displaying the message on subsequent page loads
        <?php unset($_SESSION['errorMessage']); ?>
    }
</script>





 
</body>
</html>
