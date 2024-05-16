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












// Check for an error message in the session
if (isset($_SESSION['error_message'])) {
    // Display error alert using JavaScript
    echo "<script>alert('{$_SESSION['error_message']}');</script>";

    // Clear the error message from the session
    unset($_SESSION['error_message']);
}





// Check if the form is submitted for patient deletion
if (isset($_POST['ConfirmDelete'])) {
    $patient_id_to_delete = $_POST['patient_id'];

    // Add code here to perform the patient deletion from the database
    $delete_sql = "DELETE FROM patients_table WHERE Patient_id = ?";
    
    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $patient_id_to_delete);
    
    try {
        if ($stmt->execute()) {
            // Deletion successful
            $stmt->close();
            header("Location: admin-viewallpatient.php");
            exit();
        } else {
            // Error occurred during deletion
            throw new Exception("Error: " . $stmt->error);
        }
    } catch (Exception $e) {
        // Store the error message in a session variable
        $_SESSION['error_message'] = $e->getMessage();
    
        // Close the statement
        $stmt->close();
    
        // Redirect to the same page using GET method
        header("Location: admin-viewallpatient.php");
        exit();
    }
}



$sql = "SELECT * FROM patients_table";

$result = $conn->query($sql);

// Close the database connection (you should do this when you're done fetching data)
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | List of Patients </title>


    <link rel="icon" href="images/logo.png" type="image/png">

    <link rel="stylesheet" type="text/css" href="style/admin-viewallpatient.css">
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


                <li class="active">
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

<p>ADMIN <span>/ LIST OF PATIENTS</span></p>



</div>


  



   



</div>



            <div class="inside-container">

                <div class="new-container">

                        <div class="newbtn">
                            <a href="admin-addpatient.php"><i class='bx bx-plus'></i>New</a>
                        </div>
                   

                </div>



                <div class="rectangle">
                <table id="myTable" class="display">
                <thead id="thead" >
                <tr>
                    <th>Patient ID</th>
                    <th>Full Name</th>
                    <th>Phone Number</th>
                    <th>Date of Birth</th>

                    <th>Gender</th>
                    <th>Height</th>
                    <th>Weight</th>

                    <th>Actions</th>
                    
                </tr>
                </thead>

                <tbody>
                        <?php
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['Patient_id']}</td>";
                        echo "<td>{$row['Last_Name']}, {$row['First_Name']} </td>";
                        echo "<td>{$row['Phone']}</td>";

                        $dateString = $row['Date_of_Birth'];
                        $dateTime = new DateTime($dateString);
                        $formattedDate = $dateTime->format('F j, Y');
                        echo "<td>{$formattedDate}</td>";

                        echo "<td>{$row['Gender']}</td>";
                        echo "<td>{$row['Height']}</td>";
                        echo "<td>{$row['Weight']}</td>";

                        echo "<td class='button-action'>
                                <a href='admin-viewpatient.php?patient_id={$row['Patient_id']}' class='view-button'>View <i class='bx bxs-show'></i></a>
                                <a href='admin-editpatient.php?patient_id={$row['Patient_id']}' class='edit-button'>Edit <i class='bx bxs-message-square-edit'></i></a>
                                <button class='delete-button' data-patient-id='{$row['Patient_id']}' type='button'>Delete <i class='bx bxs-checkbox-minus'></i></button>


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
                <input type="hidden" id="patient_id" name="patient_id" value="">
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
var patientIdInput = document.getElementById("patient_id");

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
