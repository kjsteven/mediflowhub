 <?php 

require 'db.php';

$token = $_REQUEST['token'];

$sql = "SELECT Activated FROM users where Token = '$token'";
$result = $conn->query($sql); 

if ($result->num_rows > 0) {
 
$sql2 = "UPDATE users SET Activated='1' WHERE Token = '$token'";
if ($conn->query($sql2) === TRUE) {
    $message = "You have successfully verified your account.";
  } else {
    echo "Error While Activating : " . $conn->error;
  }

} else {
  echo "0 results";
}



?> 



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Email Verified</title>


  
<link rel="icon" href="../users/images/logo.png" type="image/png">




<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">

</head>
<body>


<div class="container">


  <div class="check">

    <i class='bx bx-check'></i>
    
  </div>

  <p>Verified!</p>

  <span><?php echo $message; ?></span>

 

  <div class="login-btn">
    <a href="../users/login.php">Click here to Login</a>

  </div>








</div>


  
</body>
</html>

<style>


*,
*::before,
*::after{
    box-sizing: border-box;
}





:root{
    
    --clr-text: #030104;
    --clr-text-lessopacity: #62645B;

    --clr-nav-bar-color: #62645B;
    --clr-background-color: #292D32;

    --clr-primary-color: #01BCD6;
    --clr-button-color: #6C70DC;
    
    --clr-light: #FFF;
    --clr-dark: #000;


    --fw-light: 300;
    --fw-regular: 400;
    --fw-medium: 500;
    --fw-semibold: 600;
    --fw-bold: 700;

    


    

}


body {
  

  margin: 0;
  padding: 0;
  font-family: 'Poppins', sans-serif;
  font-size: 16px;
  line-height: 1.5;

  background-color: var(--clr-primary-color);






}

.container {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);

  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;

  background-color: white;
  padding: 48px;
  border-radius: 4px;
}


.check {
    background-color: var(--clr-primary-color) ;
    height: 56px;
    width: 56px;
    padding: 12px;
    border-radius: 120px;

    display: flex;
    align-items: center;
    justify-content: center;
}

.check i {
  margin: 0;
  font-size: 32px;
  color: var(--clr-light);
  font-weight: var(--fw-bold);
}


p {
  font-size: 20px;
  margin: 0;
  margin-top: 32px;
  font-weight: var(--fw-semibold);
}

span {
  font-size: 1rem;
  color: var(--clr-text-lessopacity);
}


.login-btn {
  margin-top: 56px;
}

.login-btn a{ 
  padding: 12px 24px;
  background-color: var(--clr-primary-color);
  border-radius: 8px;



  text-decoration: none;
  color: white;
}




</style>