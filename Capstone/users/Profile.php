<?php

require_once '../session/session_manager.php';

session_start(); // Start the session

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username']; // Retrieve the username from the session
    $userID = $_SESSION['user_id']; 

    // Include the database connection file
    require '../session/db.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['confirm_account_changes'])) {
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $email = $_POST['email'];   
            $phone_no = $_POST['phone_no'];
            $dob = $_POST['dob'];
    
            // Validate and sanitize input data as needed
    
            // Update the user profile information in the database
            $stmtUpdateInfo = $conn->prepare("UPDATE users SET `First Name` = ?, `Last Name` = ?, Email = ?, `Phone Number` = ?, Date_of_Birth = ? WHERE Email = ?");
            $stmtUpdateInfo->bind_param("ssssss", $first_name, $last_name, $email, $phone_no, $dob, $_SESSION['username']);
            $stmtUpdateInfo->execute();
            $stmtUpdateInfo->close();
    
                    // Fetch the current profile image path from the database
            $stmtFetchImage = $conn->prepare("SELECT `Profile_Image` FROM users WHERE Email = ?");
            $stmtFetchImage->bind_param("s", $_SESSION['username']);
            $stmtFetchImage->execute();
            $stmtFetchImage->bind_result($currentProfileImagePath);
            $stmtFetchImage->fetch();
            $stmtFetchImage->close();

            // Delete the previous profile image if it exists
            if (!empty($currentProfileImagePath) && file_exists($currentProfileImagePath)) {
                unlink($currentProfileImagePath);
            }

            // Handle profile image upload
            if ($_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {  
                $tempFilePath = $_FILES['profile_image']['tmp_name'];
                $newFilePath = '../admin/user-image/upload/images/' . $_FILES['profile_image']['name'];

                if (move_uploaded_file($tempFilePath, $newFilePath)) {
                    // Update the database with the new file path
                    $stmtUpdateImage = $conn->prepare("UPDATE users SET `Profile_Image` = ? WHERE Email = ?");
                    $stmtUpdateImage->bind_param("ss", $newFilePath, $_SESSION['username']);
                    $stmtUpdateImage->execute();
                    $stmtUpdateImage->close();
                } else {
                    // Handle file upload failure
                    echo "Failed to move the uploaded file.";
                }
            }
    
            // Set success message and redirect
            $_SESSION['successMessage'] = "Account changes saved successfully.";
            header("Location: Profile.php");
            exit;
        }
    }
    


    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['confirm_password_changes'])) {
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $conf_password = $_POST['conf_password'];
    
            // Validate and sanitize input data as needed
    
            // Check if the current password is correct
            $stmt = $conn->prepare("SELECT Password, PasswordHistory FROM users WHERE Email = ?");
            $stmt->bind_param("s", $_SESSION['username']);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stored_password = $row['Password'];
            $password_history = json_decode($row['PasswordHistory'], true);
    
            if (password_verify($current_password, $stored_password)) {
                // Check if the new password and confirm password match
                if ($new_password === $conf_password) {
                    // Check password complexity
                    if (strlen($new_password) < 12 || !preg_match('/[A-Za-z]/', $new_password) || !preg_match('/\d/', $new_password) || !preg_match('/[^A-Za-z0-9]/', $new_password)) {
                        // Set error message for password complexity requirements not met and redirect
                        $_SESSION['errorMessage'] = "Password must have a minimum of 12 characters containing letters, numbers, and special characters.";
                        header("Location: Profile.php");
                        exit;
                    }
    
                    // Check if the password history exists and new password is in the history
                    if ($password_history !== null && in_array($new_password, $password_history)) {
                        // Set error message for using a previous password and redirect
                        $_SESSION['errorMessage'] = "Cannot reuse a previous password.";
                        header("Location: Profile.php");
                        exit;
                    }
    
                    // Update the password in the database
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    
                    // Add hashed new password to history
                    $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT); 
                    if ($password_history === null) {
                        $password_history = []; // Initialize password history if it's null
                    }
                    
                    // Keep only the last 5 passwords
                    if (count($password_history) >= 5) {
                        array_shift($password_history);
                    }
                    
                    $password_history[] = $hashed_new_password; 
                    
                    $password_history_json = json_encode($password_history);
                    $stmt = $conn->prepare("UPDATE users SET Password = ?, PasswordHistory = ? WHERE Email = ?");
                    $stmt->bind_param("sss", $hashed_password, $password_history_json, $_SESSION['username']);
                    $stmt->execute();
    
                    // Set success message and redirect
                    $_SESSION['successMessage'] = "Password changed successfully.";
                    header("Location: Profile.php");
                    exit;
                } else {
                    // Set error message for mismatched passwords and redirect
                    $_SESSION['errorMessage'] = "New password and confirm password do not match.";
                    header("Location: Profile.php");
                    exit;
                }
            } else {
                // Set error message for incorrect current password and redirect
                $_SESSION['errorMessage'] = "Incorrect current password.";
                header("Location: Profile.php");
                exit;
            }
        }
    }
    


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['confirm_address_changes'])) {
            
            $region = $_POST['region_text'];
            $province = $_POST['province_text'];
            $city = $_POST['city_text'];
            $barangay = $_POST['barangay_text'];
            $zip_code = $_POST['zip_code'];
    
            // Validate and sanitize input data as needed
    
            // Start a transaction to ensure data consistency
            $conn->begin_transaction();
    
            try {
                // Retrieve the user_id for the current user
                $user_id_query = $conn->prepare("SELECT user_id FROM users WHERE Email = ?");
                $user_id_query->bind_param("s", $_SESSION['username']);
                $user_id_query->execute();
                $user_id_result = $user_id_query->get_result();
                $user_id_row = $user_id_result->fetch_assoc();
                $user_id = $user_id_row['user_id'];
    
                // Check if the user already has an address
                $address_query = $conn->prepare("SELECT address_id FROM address_table WHERE user_id = ?");
                $address_query->bind_param("i", $user_id);
                $address_query->execute();
                $address_result = $address_query->get_result();
                $address_row = $address_result->fetch_assoc();
    
                if ($address_row) {
                    // Update the existing address
                    $update_stmt = $conn->prepare("UPDATE address_table SET Region = ?, Province = ?, Municipality = ?, Barangay = ? WHERE user_id = ?");
                    $update_stmt->bind_param("ssssi", $region, $province, $city, $barangay, $user_id);
                    $update_stmt->execute();
                } else {
                    // Insert a new address
                    $insert_stmt = $conn->prepare("INSERT INTO address_table (user_id, Region, Province, Municipality, Barangay) VALUES (?, ?, ?, ?, ?)");
                    $insert_stmt->bind_param("issss", $user_id, $region, $province, $city, $barangay);
                    $insert_stmt->execute();
                }
    
                // Commit the transaction if everything is successful
                $conn->commit();
    
                // Set success message and redirect
                $_SESSION['successMessage'] = "Address changes saved successfully.";
                header("Location: Profile.php");
                exit;
            } catch (Exception $e) {
                // Rollback the transaction in case of an error
                $conn->rollback();
    
                // Handle the error as needed
                echo "Error: " . $e->getMessage();
            }
        }
    }
    


            // Fetch user profile image path from the database
        $stmtFetchImage = $conn->prepare("SELECT `Profile_Image` FROM users WHERE Email = ?");
        $stmtFetchImage->bind_param("s", $_SESSION['username']);
        $stmtFetchImage->execute();
        $stmtFetchImage->bind_result($profileImagePath);
        $stmtFetchImage->fetch();
        $stmtFetchImage->close();
    
        // Fetch user details including address
        $stmt = $conn->prepare("SELECT a.* FROM address_table a
        JOIN users u ON a.user_id = u.user_id
        WHERE u.Email = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    
        $region = $row['Region'];
        $province = $row['Province'];
        $municipality = $row['Municipality'];
        $barangay = $row['Barangay'];

        // Construct the full address
        $full_address = " $barangay, $municipality, $province, $region";
        } else {
        // Handle the case where no address is found for the user
        $full_address = "Address not available";
        }

        // Close the prepared statement
        $stmt->close();
 
 
    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT `Last Name`, `First Name`, Email, Password, `Phone Number`, Date_of_Birth FROM users WHERE Email = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_name = $row['Last Name'];
        $first_name = $row['First Name'];
        $email = $row['Email'];
        $phone_no = $row['Phone Number'];
        $dob = $row['Date_of_Birth'];
    }

    // Close the prepared statement
    $stmt->close();


    

} else {
    header("Location: login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | Profile </title>


    <link rel="icon" href="images/logo.png" type="image/png">

    <link rel="stylesheet" type="text/css" href="style/profile.css">
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

                        <li class="active">
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
           
            <div class="user--info">

                      
                     

                        
                        <div class="dropdown-profile">

                            <div class="sub-menu">

                                    <div class="user-info">
                                        <button class="usermain-profile"></button>
                                        <p>Username</p>
                                    </div>

                                    <div class="edit-profile">
                                        <div class="edit-profile1">
                                        <i class='bx bxs-user-circle' ></i>
                                        <p>Edit Profile</p>
                                        </div>
                                    
                                        <i class='bx bx-chevron-right' ></i>
                                    </div>

                                    <div class="help-support">
                                        <div class="edit-profile1">
                                        <i class='bx bxs-help-circle' ></i>
                                        <p>Help & Support</p>
                                        </div>
                                        <i class='bx bx-chevron-right' ></i>
                                    </div>



                            </div>


                        </div>

                        <div class="dropdown-notifications">
                                <p class="Notiftitle">Notifications</p>

                                <p class="ReminderTitle">Reminder</p>
                               
                                <div class="notif-box">

                                        <div class="notif-message">
                                            <p>Your appointment with Dr. Quack Quack starts in 1hr.</p>

                                            <i class='bx bx-chevron-right'></i>

                                        </div>

                                        <div class="notif-time">

                                             <i class='bx bxs-time-five'></i>
                                             <p>Now</p>
                                            
                                        </div>
                                       

                                </div>

                                <div class="notif-box">

                                        <div class="notif-message">
                                            <p>Your appointment with Dr. Quack Quack starts in 1hr.</p>

                                            <i class='bx bx-chevron-right'></i>

                                        </div>

                                        <div class="notif-time">

                                             <i class='bx bxs-time-five'></i>
                                             <p>Now</p>
                                            
                                        </div>
                                       

                                </div>




                        </div>



                       

            </div>
           
        </div>


        <div class="first-container">
            <h1>Account settings</h1>


            
            <div id="successMessage" class="success-message"><i id="bx-check" class='bx bx-check'></i> </div>

            <div id="errorMessage" class="errorMessage"><i id="bx-error" class='bx bx-x-circle'></i> </div>

            <div class="account-container">

                <div class="account-options">

                        <li id="profile-btn" class="active">
                            <i class='bx bxs-user-circle'></i>
                            <p>Profile</p>
                        </li>

                        <li id="address-btn" class="active3">
                            <i class='bx bxs-edit-location'></i>
                            <p>Address</p>
                        </li>
                            
                   
                        <li id="password-btn" class="active2" >
                            <i class='bx bxs-lock-alt' ></i>
                            <p>Password</p>
                        </li>

                </div>


                <div id="account-edit" class="account-edit">

                    <form class="account-input" action="Profile.php" method="POST" enctype="multipart/form-data">

                    <div class="image-edit">

                    <div class="image-prof">
                        <?php if (!empty($profileImagePath)): ?>
                            <img id="profile-image" src="<?php echo $profileImagePath; ?>" alt="User Profile Image">
                        <?php else: ?>
                            <img id="profile-image" src="images/PROFILE1.png" alt="Default Profile Image">
                        <?php endif; ?>
                        <i id="upload-icon" class="bx bxs-camera"></i>
                        <input type="file" id="image-upload" name="profile_image" style="display: none;">
                    </div>



                                    <div class="image-buttons">

                                    
                                        <button class="delete-btn">Profile</button>

                                    </div>
                        

                    </div>

                    <div class="input-container">


            

                        <div class="input-box">
                                <p>FIRST NAME<span>*</span></p>
                                <input type="text" name="first_name"  placeholder="First Name....." required="required" value="<?php echo $first_name; ?>" readonly>
                                
                        </div>

                        <div class="input-box">
                                <p>LAST NAME <span>*</span></p>
                                <input type="text"  name="last_name"  placeholder="Last Name....." required="required" value="<?php echo $last_name; ?>" readonly>
                                
                        </div>

                        <div class="input-box">
                                <p>EMAIL <span>*</span></p>
                                <input type="text" name="email"   placeholder="Email Address....." required="required" value="<?php echo $email; ?>" readonly>
                                
                        </div>

                        <div class="input-box">
                                <p>MOBILE NO. <span>*</span></p>
                                <input type="text" name="phone_no" placeholder="63+" required="required" value="<?php echo $phone_no; ?>">
                                
                        </div>

                        <div class="input-box">
                                <p>Date of Birth. <span>*</span></p>
                                <input type="date"  name="dob"  required="required" value="<?php echo $dob; ?>">
                                
                        </div>


                      
                    <!-- 
                        <div class="input-radio">
                                <p>GENDER <span>*</span></p>

                                <div class="radio-inputs">

                                        <div class="gender">
                                            <input type="radio" id="gender" name="age" value="30">
                                            <label for="gender">Male</label><br>

                                        </div>

                                        <div class="gender">
                                            <input type="radio" id="gender" name="age" value="30">
                                            <label for="gender">Female</label><br>

                                        </div>
                                            

                                            

                                </div>

                                
                        </div>  -->

                        <div class="confirmchanges">
                       
                       <button type="submit" class="confirm-btn1" name="confirm_account_changes">Confirm Changes</button>
                        </div>


                        </div>

                    </form>


                   



                </div>








                <div id="password-edit" class="password-edit">
                <form class="form-input" action="Profile.php" method="POST">
                    <div class="pass-box">
                        <p>CURRENT PASSWORD <span>*</span></p>
                        <input type="password" name="current_password" id="current_password" placeholder="Current Password....." required="required">
                        <input  class="checkbox" type="checkbox" onclick="togglePassword('current_password')" > Show
                    </div>

                    <div class="pass-box">
                        <p>NEW PASSWORD <span>*</span></p>
                        <input type="password" name="new_password" id="new_password" placeholder="New Password....." required="required">
                        <input type="checkbox" onclick="togglePassword('new_password')"> Show
                    </div>

                    <div class="pass-box">
                        <p>CONFIRM PASSWORD <span>*</span></p>
                        <input type="password" name="conf_password" id="conf_password" placeholder="Confirm Password....." required="required">
                        <input type="checkbox" onclick="togglePassword('conf_password')"> Show
                    </div>

                    <div class="confirmpassword">
                        <button type="submit" class="confirm-btn" name="confirm_password_changes">Confirm Changes</button>
                    </div>
                </form>
            </div>










                 <div id="address-edit" class="address-edit">

                   
                 <form  class="form-input-address" action="Profile.php" method="POST">

                        <div class="input-box">
                                <p>Full Address<span>*</span></p>
                                <input type="text" name="full_address" value="<?php echo htmlspecialchars($full_address); ?>"  readonly>
                                
                        </div>


                     
                        <p>Region, Province, City, Barangay<span>*</span> </p>
                  


                        <div class="select-container">



                                <div class="select-box">
                        
                                    <select id="region" ></select>
                                    <input type="hidden" name="region_text" id="region-text" >
                                </div>


                                <div class="select-box">
                                    <select id="province"></select>
                                    <input type="hidden" name="province_text" id="province-text"  >
                                </div>

                                <div class="select-box">
                                    <select id="city"></select>
                                    <input type="hidden" name="city_text" id="city-text"  >
                                </div>


                                <div class="select-box">
                                    <select id="barangay"></select>
                                    <input type="hidden" name="barangay_text" id="barangay-text"  >
                                </div>

            
                               

                        </div>

                        


                        <div class="input-box">
                                <p>Zip Code<span>*</span></p>
                                <input type="text" name="zip_code'" placeholder="Zip Code....." required="required">
                                
                        </div>



                        
                    <div class="confirmchanges">
                             <button type="submit" class="confirm-btn" name="confirm_address_changes">Confirm Changes</button>
                    </div>
                                                    

                    


                    </form>






                </div>






            </div>


          



            




        </div>






      


    </div>


    <script src="script/script.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>


 
</body>

<script>
    function togglePassword(inputId) {
        var x = document.getElementById(inputId);
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }
</script>




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


                var my_handlers = {
                // fill province
                fill_provinces: function() {
                    //selected region
                    var region_code = $(this).val();

                    // set selected text to input
                    var region_text = $(this).find("option:selected").text();
                    let region_input = $('#region-text');
                    region_input.val(region_text);
                    //clear province & city & barangay input
                    $('#province-text').val('');
                    $('#city-text').val('');
                    $('#barangay-text').val('');

                    //province
                    let dropdown = $('#province');
                    dropdown.empty();
                    dropdown.append('<option selected="true" disabled>Choose State/Province</option>');
                    dropdown.prop('selectedIndex', 0);

                    //city
                    let city = $('#city');
                    city.empty();
                    city.append('<option selected="true" disabled></option>');
                    city.prop('selectedIndex', 0);

                    //barangay
                    let barangay = $('#barangay');
                    barangay.empty();
                    barangay.append('<option selected="true" disabled></option>');
                    barangay.prop('selectedIndex', 0);

                    // filter & fill
                    var url = 'ph-json/province.json';
                    $.getJSON(url, function(data) {
                        var result = data.filter(function(value) {
                            return value.region_code == region_code;
                        });

                        result.sort(function(a, b) {
                            return a.province_name.localeCompare(b.province_name);
                        });

                        $.each(result, function(key, entry) {
                            dropdown.append($('<option></option>').attr('value', entry.province_code).text(entry.province_name));
                        })

                    });
                },
                // fill city
                fill_cities: function() {
                    //selected province
                    var province_code = $(this).val();

                    // set selected text to input
                    var province_text = $(this).find("option:selected").text();
                    let province_input = $('#province-text');
                    province_input.val(province_text);
                    //clear city & barangay input
                    $('#city-text').val('');
                    $('#barangay-text').val('');

                    //city
                    let dropdown = $('#city');
                    dropdown.empty();
                    dropdown.append('<option selected="true" disabled>Choose city/municipality</option>');
                    dropdown.prop('selectedIndex', 0);

                    //barangay
                    let barangay = $('#barangay');
                    barangay.empty();
                    barangay.append('<option selected="true" disabled></option>');
                    barangay.prop('selectedIndex', 0);

                    // filter & fill
                    var url = 'ph-json/city.json';
                    $.getJSON(url, function(data) {
                        var result = data.filter(function(value) {
                            return value.province_code == province_code;
                        });

                        result.sort(function(a, b) {
                            return a.city_name.localeCompare(b.city_name);
                        });

                        $.each(result, function(key, entry) {
                            dropdown.append($('<option></option>').attr('value', entry.city_code).text(entry.city_name));
                        })

                    });
                },
                // fill barangay
                fill_barangays: function() {
                    // selected barangay
                    var city_code = $(this).val();

                    // set selected text to input
                    var city_text = $(this).find("option:selected").text();
                    let city_input = $('#city-text');
                    city_input.val(city_text);
                    //clear barangay input
                    $('#barangay-text').val('');

                    // barangay
                    let dropdown = $('#barangay');
                    dropdown.empty();
                    dropdown.append('<option selected="true" disabled>Choose barangay</option>');
                    dropdown.prop('selectedIndex', 0);

                    // filter & Fill
                    var url = 'ph-json/barangay.json';
                    $.getJSON(url, function(data) {
                        var result = data.filter(function(value) {
                            return value.city_code == city_code;
                        });

                        result.sort(function(a, b) {
                            return a.brgy_name.localeCompare(b.brgy_name);
                        });

                        $.each(result, function(key, entry) {
                            dropdown.append($('<option></option>').attr('value', entry.brgy_code).text(entry.brgy_name));
                        })

                    });
                },

                onchange_barangay: function() {
                    // set selected text to input
                    var barangay_text = $(this).find("option:selected").text();
                    let barangay_input = $('#barangay-text');
                    barangay_input.val(barangay_text);
                },

                };


                $(function() {
                // events
                $('#region').on('change', my_handlers.fill_provinces);
                $('#province').on('change', my_handlers.fill_cities);
                $('#city').on('change', my_handlers.fill_barangays);
                $('#barangay').on('change', my_handlers.onchange_barangay);

                // load region
                let dropdown = $('#region');
                dropdown.empty();
                dropdown.append('<option selected="true" disabled>Choose Region</option>');
                dropdown.prop('selectedIndex', 0);
                const url = 'ph-json/region.json';
                // Populate dropdown with list of regions
                $.getJSON(url, function(data) {
                    $.each(data, function(key, entry) {
                        dropdown.append($('<option></option>').attr('value', entry.region_code).text(entry.region_name));
                    })
                });

                });








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


<script>

var profilebtn = document.getElementById("profile-btn");
var passwordbtn = document.getElementById("password-btn");
var addressbtn = document.getElementById("address-btn");


var accountedit = document.getElementById("account-edit");
var passwordedit = document.getElementById("password-edit");
var addressedit = document.getElementById("address-edit");




profilebtn.addEventListener("click", () => {
  accountedit.style.display = "block";
  passwordedit.style.display = "none";
  addressedit.style.display = "none";

  profilebtn.style.backgroundColor = "rgba(91, 91, 91, 0.20)"
  passwordbtn.style.backgroundColor = "#F5F6F8";
  addressbtn.style.backgroundColor = "#F5F6F8"

});


addressbtn.addEventListener("click", () => {
  addressedit.style.display = "block";
  accountedit.style.display = "none";
  passwordedit.style.display = "none";

  addressbtn.style.backgroundColor = "rgba(91, 91, 91, 0.20)"
  passwordbtn.style.backgroundColor = "#F5F6F8";
  profilebtn.style.backgroundColor = "#F5F6F8"

});


passwordbtn.addEventListener("click", () => {
  passwordedit.style.display = "block";
  accountedit.style.display = "none";
  addressedit.style.display = "none";

  profilebtn.style.backgroundColor = "#F5F6F8"
  passwordbtn.style.backgroundColor = "rgba(91, 91, 91, 0.20)";
  addressbtn.style.backgroundColor = "#F5F6F8"

});


</script>









</html>
