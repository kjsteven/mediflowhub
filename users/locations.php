<?php
require '../session/db.php';
require_once '../session/session_manager.php';


start_secure_session();


// Your other code here

if (!isset($_SESSION["username"])) {
    header("Location: login.php"); 
    exit;
}


$sql = "SELECT Clinic_ID, Clinic_Name, Address FROM `clinic_info`";
$result = $conn->query($sql);



// Close the database connection (if needed)
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locations</title>


    <link rel="icon" href="images/logo.png" type="image/png">

    <link rel="stylesheet" type="text/css" href="style/locations.css">
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
                                    <a href="#">Book Appointments</a>

                            </div>

                        </li>


                        
                        <li>
                            <a href="availabledoctors.php">
                                <i class='bx bxs-user-rectangle' ></i>
                                <span>Doctors</span>
                            </a>
                        </li>

                    

                        <li class="active">
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
                   <a href="https://www.facebook.com/messages/t/61559867466389" target="_blank">
                   <i class='bx bxs-chat'></i>
                   <span>Chat Support</span>
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

                        <div class="search-box">
                        
                        <input type="text" placeholder="Search...">
                        <i class='bx bx-search'></i>
                    
                        </div>

            </div>
           
            
           
        </div>


        <div class="first-container">
            <h1>Health Centers near you</h1>

     

            <div class="hospital-container">

            <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                ?>

          

                <div class="hospital-info">

                        <h2><?php echo $row['Clinic_Name']; ?></h2>
                        <p class="title-hospital">Health Center</p>

                        <img src="images/hospital.png" alt="">

                        <p>Address</p>

                        <p class="address"><?php echo $row['Address']; ?>
                        </p>

                        <p>Office Hours</p>

                        <p class="hours">8:00 am to 10:30 pm</p>

                       <!--  <button class="directions-btn">Directions</button> -->



                </div>

                <?php
                        }
                    } else {
                        echo "No data found in the 'doctors-table'.";
                    }
                ?>


             

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




 
</body>
</html>
