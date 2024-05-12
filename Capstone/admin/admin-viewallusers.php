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



$sql = "SELECT * FROM users";

$result = $conn->query($sql);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['new_role'];

    // Update the status in the database
    $updateQuery = "UPDATE users SET Role = '$new_role' WHERE user_id = $user_id";
    $updateResult = $conn->query($updateQuery);

    if ($updateResult === false) {
        die("Error updating the status: " . $conn->error);
    }

    // Redirect back to the appointments page after updating the status
    header("Location: admin-viewallusers.php");
    exit();
}





// Close the database connection (you should do this when you're done fetching data)
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | List of Users </title>


    <link rel="icon" href="images/logo.png" type="image/png">

    <link rel="stylesheet" type="text/css" href="style/admin-viewallusers.css">
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





<div class="main--content">

<div class="header--wrapper">



 
<div class="Logo">


<a href="admin-dashboard.php"><img src="images/logo.png" alt="" width="50px" /></a>
            
            

</div>


<div class="navigation">

<p>ADMIN <span>/ LIST OF USERS</span></p>



</div>


  



   



</div>



<div class="inside-container">
    <div class="rectangle">
        <table id="myTable" class="display">
            <thead id="thead" >
            <tr>
                <th>User ID</th>
                <th>Full Name</th>
                <th>Email </th>
                <th>Phone Number </th>
                <th>Role</th>
                <th>Action</th>
                
            </tr>
            </thead>

            <tbody>

            <?php
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['user_id']}</td>";
                        echo "<td>{$row['Last Name']}, {$row['First Name']} </td>";
                        echo "<td>{$row['Email']}</td>";

                        echo "<td>{$row['Phone Number']}</td>";
                        echo "<td>" . ucwords(strtolower($row['Role'])) . "</td>";



                        echo "<td>
                        <form action='' method='post'>
                            <input type='hidden' name='user_id'' value='{$row['user_id']}'>
                            <select name='new_role'>
                                <option value='User'>User</option>
                                <option value='Admin'>Admin</option>
                            </select>
                            <input type='submit' value='Update'>
                        </form>
                    </td>";


                    
                        
                    
                        echo "</tr>";
                    }
            ?>


                </tbody>

        </table>
    </div>




    <div class="backbtn">

                    <a href="admin-dashboard.php">Back to dashboard</a>

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
