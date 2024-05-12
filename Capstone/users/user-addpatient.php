<?php
require_once '../session/session_manager.php';
require '../session/db.php';


start_secure_session();


// Your other code here

if (!isset($_SESSION["username"])) {
    header("Location: login.php"); 
    exit;
}


$userID = $_SESSION["user_id"];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $firstName = ucwords(strtolower($_POST['FirstName']));
    $lastName = ucwords(strtolower($_POST['LastName']));
    $birthday = $_POST['Date'];
    $phone = $_POST['Phone'];
    $gender = $_POST['gender'];
    $weight = $_POST['Weight'];
    $height = $_POST['Height'];

    // TODO: Validate and sanitize input data

    // Prepare and bind the SQL statement
    $stmt = $conn->prepare("INSERT INTO patients_table (user_id, First_Name, Last_Name, Date_of_Birth, Phone, Gender, Weight, Height) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssssss", $userID, $firstName, $lastName, $birthday, $phone, $gender, $weight, $height);

// Execute the prepared statement
$result = $stmt->execute();
    // Check if the query was successful
    if ($result) {
        $_SESSION['successMessage'] = "Patient added successfully.";
    } else {
        $_SESSION['errorMessage'] = "Error adding patient. Please try again.";
    }

    // Close the prepared statement and database connection
    $stmt->close();
    $conn->close();

    // Redirect to the same page to prevent form resubmission on page refresh
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}
?>






<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User | Add Patient</title>


    <link rel="icon" href="images/logo.png" type="image/png">

    <link rel="stylesheet" type="text/css" href="style/user-addpatient.css">
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


                <div class="header--wrapper">
                    
                    <div class="menu-search">
                    
                                <i class='bx bx-menu' id="menu-toggle"></i>

                                

                    </div>
                    
               
                    
                </div>



                <div class="first-container">

             



                            <div class="inside-container">


                                    <div class="title-1">
                                                <p>New patient</p>

                                    </div>

                                    <div id="successMessage" class="success-message"><i id="bx-check" class='bx bx-check'></i> </div>

                                    <div id="errorMessage" class="errorMessage"><i id="bx-error" class='bx bx-x-circle'></i> </div>


                                    <form action="" method="post" >

                                                <div class="input-container">

                                    
                                                    <label for="FirstName">First Name<span>*</span></label>
                                            
                                                            
                                                    <input type="text" name="FirstName" placeholder="Enter First Name" required="required">
                                                        
                                            

                                                </div>


                                                <div class="input-container">

                                                                                    
                                                <label for="FirstName">Last Name<span>*</span></label>

                                                        
                                                <input type="text" name="LastName" placeholder="Enter Last Name" required="required">
                                                    


                                                </div>

                                                <div class="input-container">

                                                                                        
                                                    <label for="Date">Birthday<span>*</span></label>

                                                            
                                                    <input type="date" name="Date"  required="required">
                                                        


                                                </div>


                                                <div class="input-container">

                                                                                                                                            
                                                    <label for="Phone">Phone:</label>

                                                            
                                                    <input type="tel" name="Phone"  required="required">
                                                        


                                                </div>


                                                
                                                <div class="input-container">

                                                                                                                                            
                                                <label for="gender">Gender:</label>

                                                        
                                                <select name="gender" id="">
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                    <option value="Rather Not Say">Rather Not Say</option>
                                                </select>


                                                </div>


                                                <div class="input-container">

                                                                                                                                            
                                                    <label for="Weight">Weight:</label>

                                                            
                                                    <input type="text" name="Weight"  placeholder="kg" required="required">
                                                        


                                                </div>


                                                <div class="input-container">

                                                                                                                                            
                                                    <label for="Height">Height:</label>

                                                            
                                                    <input type="text" name="Height"  placeholder="m" required="required">
                                                        


                                                </div>




                                                <button type="submit" class="savebtn">Save</button>



                                    
                        
                                        
                                    </form>


             



                                </div>

                                                       
                    <div class="backbtn">

                    <a href="bookappointment.php">Back to Book Appointments</a>

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
