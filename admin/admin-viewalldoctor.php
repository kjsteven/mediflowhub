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




$sql = "SELECT doctors_table.*, clinic_info.Clinic_Name
        FROM doctors_table
        JOIN clinic_info ON doctors_table.Clinic_ID = clinic_info.Clinic_ID";

$result = $conn->query($sql);

// Close the database connection (you should do this when you're done fetching data)
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | List of Doctors </title>


    <link rel="icon" href="images/logo.png" type="image/png">

    <link rel="stylesheet" type="text/css" href="style/admin-viewalldoctor.css">
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
    });

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


<div class="navigation">

<p>ADMIN <span>/ LIST OF DOCTOR</span></p>



</div>

  



   



</div>



<div class="inside-container">
    <div class="rectangle">
    <table id="myTable" class="display">
    <thead id="thead" >
    <tr>
        <th>Doctor ID</th>
        <th>Email</th>
        <th>Full Name</th>
        <th>Specialty</th>
        <th>Experience</th>
        <th>Fee</th>
        <th>Clinic</th>
        <th>Phone Number</th>
        <th>Action</th>
       <!--  <th>Schedule Availability</th>
        <th>View</th> -->
    </tr>
    </thead>

    <tbody>


    <?php
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['doctor_id']}</td>";
                echo "<td>{$row['Email']}</td>";
                echo "<td>{$row['First_Name']} {$row['Last_Name']}</td>";
                echo "<td>{$row['Specialty']}</td>";
                echo "<td>{$row['Experience']} yrs.</td>";
                echo "<td>₱" . number_format($row['Fee'], 2) . "</td>";
                echo "<td>{$row['Clinic_Name']}</td>";
                echo "<td>{$row['Phone_Number']}</td>";

                
                echo "<td class='button-action'>
                <a href='admin-viewdoctor.php?doctor_id={$row['doctor_id']}' class='view-button'>View <i class='bx bxs-show'></i></a>
                <a href='admin-editdoctor.php?doctor_id={$row['doctor_id']}' class='edit-button'>Edit <i class='bx bxs-message-square-edit'></i></a>
               


                </td>";
                echo "</tr>";
            }
    ?>


</tbody>
         

        </table>
    </div>




</div>




</div>


<div class="delete-modal" id="delete-modal">
    <div class="delete-modal-content">
        <p id="modal-message">Are you sure you want to remove this patient?</p>
        <div class="modal-buttons">
            <button id="close-btn" class="close-btn">Close</button>
            <form id="deleteForm" action="" method="post">
                <input type="hidden" id="doctor_id" name="doctor_id" value="">
                <button type="submit" class="confirm" name="ConfirmDelete">Confirm</button>
            </form>
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



<script>
// Get the modal
var modal = document.getElementById("delete-modal");
var span = document.getElementById("close-btn");
var patientIdInput = document.getElementById("doctor_id");

var deleteBtns = document.querySelectorAll(".delete-button");
deleteBtns.forEach(function (deleteBtn) {
    deleteBtn.onclick = function() {
        var patientId = this.dataset.patientId;
        patientIdInput.value = patientId;

        // Update the content of the modal with the patient ID
        var modalContent = modal.querySelector(".delete-modal-content p");
        modalContent.innerHTML = "Are you sure you want to remove patient with ID: " + patientId + "?";

        modal.style.display = "block";
        return false;
    };
});


// When the user clicks on "Close", close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks on "Confirm", submit the delete form
var confirmBtn = document.querySelector(".confirm");
confirmBtn.onclick = function() {
    document.getElementById("deleteForm").submit();
}






// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}
</script>








 
</body>
</html>
