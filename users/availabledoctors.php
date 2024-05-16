<?php
require '../session/db.php';
require_once '../session/session_manager.php';



start_secure_session();


// Your other code here

if (!isset($_SESSION["username"])) {
    header("Location: login.php"); 
    exit;
}

// Initialize a variable to count the number of doctors
$numDoctors = 0;

// Set a default value for the selected specialty
$selectedSpecialty = isset($_POST['specialty']) ? $_POST['specialty'] : '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the selected specialty from the form
    $selectedSpecialty = $_POST['specialty'];
}

// Modify the SQL query to include a WHERE clause based on the selected specialty
$sql = "SELECT doctor_id, First_Name, Last_Name, Specialty, Experience, Fee FROM `doctors_table`";

if (!empty($selectedSpecialty)) {
    $sql .= " WHERE Specialty = '$selectedSpecialty'";
}

$result = $conn->query($sql);

// Update the number of doctors based on the result set
$numDoctors = $result !== false ? $result->num_rows : 0;

// Close the database connection (if needed)
$conn->close();
?>











<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Doctors</title>

    <link rel="icon" href="images/logo.png" type="image/png">


    <link rel="stylesheet" type="text/css" href="style/availabledoctors.css">
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

                        
                    
                        <li>
                            <button class="dropdown-btn">
                                <i class='bx bxs-time-five'></i>
                                <span>Appointments</span>
                                <i class='bx bxs-chevron-down'></i>
                            </button>

                            <div class="dropdown-container">
                                    <a href="appointments.php">View Appointments</a>
                                    <a href="bookappointment.php">Book Appointments</a>

                            </div>

                        </li>


                        
                        <li class="active">
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

                        <div class="menu1">

                                <i class='bx bx-menu' id="menu-toggle"></i>

                                <div class="search-box">

                                <input type="text" placeholder="Search...">
                                <i class='bx bx-search'></i>

                                </div>

                        </div>


                       
                        <div class="menu3">

                             
                            
                                <form method="post" action="">
                                    <select name="specialty" id="specialtyFilter">
                                        <option value="">All Specialty</option>
                                        <option value="InfantVaccine">Infant Vaccine</option>
                                        <option value="Consultation">Consultation</option>
                                        <option value="Mental Health">Mental Health</option>
                                        <option value="FamilyPlanning">Family Planning</option>
                                        <option value="Psychiatrists">Psychiatrists</option>
                                        <option value="Pediatrician">Pediatrician</option>
                                        <option value="AnimalVaccine">Animal Vaccine</option>
                                    </select>
                                    <button type="submit">Apply Filter</button>
                                </form>

                        </div>
                        
            </div>

     
            
           
        </div>


        

       
        <div class="number-results">
    <p>We've found <?php echo $numDoctors; ?> Doctors/Providers you can book with!</p>
</div>

       


        <div class="doctors-results">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                ?>

                <div class="doctors-container">
                    <div class="information">
                        <div class="doctors-information">
                            <button class="profile-image"></button>
                            <div class="doctors-names">
                                <p class="doctor-name"><?php echo 'Dr. ' . $row['First_Name'] . ' ' . $row['Last_Name']; ?></p>
                                <p class="profession"><?php echo $row['Specialty']; ?></p>
                                <p class="profession"><?php echo $row['Experience'] . ' yrs. of experience'; ?></p>
                            </div>
                        </div>
                        <div class="schedule">
                            <p class="title">EARLIEST AVAILABLE SCHEDULE</p>
                            <p class="time">Today, 09:00 AM - 11:00 PM</p>
                            <p class="time">Fee: ₱<?php echo number_format($row['Fee'], 2); ?></p>
                        </div>
                    </div>



                    <form action="individual-doctor.php" method="GET">
                        <input type="hidden" name="doctor_id" value="<?php echo $row['doctor_id']; ?>">
                        <button type="submit" class="viewdoctor">VIEW DOCTOR</button>
                    </form>




                </div>

                
                <?php
                    }
                } else {
                    echo "No data found in the 'doctors-table'.";
                }
                ?>
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




    
</body>
</html>