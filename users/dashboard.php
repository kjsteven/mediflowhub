<?php
// Include the session manager
require_once '../session/session_manager.php';
require '../session/db.php';


start_secure_session();


// Your other code here

if (!isset($_SESSION["username"])) {
    header("Location: login.php"); 
    exit;
}

$firstName = $_SESSION["first_name"];
$username = $_SESSION["username"];


  // Fetch user profile image path from the database
  $stmtFetchImage = $conn->prepare("SELECT `Profile_Image` FROM users WHERE Email = ?");
  $stmtFetchImage->bind_param("s", $_SESSION['username']);
  $stmtFetchImage->execute();
  $stmtFetchImage->bind_result($profileImagePath);
  $stmtFetchImage->fetch();
  $stmtFetchImage->close();




?>


<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>


    <link rel="icon" href="images/logo.png" type="image/png">

    <link rel="stylesheet" type="text/css" href="style/dashboard.css">
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

                <li class="active">
                    <a href="dashboard.php" >
                        <i class='bx bxs-dashboard'></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                
            
                <li>
                    <button class="dropdown-btn">
                        <i class='bx bxs-time-five'></i>
                        <span>Appointments</span>
                        <i id="chevron-down" class='bx bxs-chevron-down'></i>
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
    <a href="https://www.facebook.com/kjsteven09" target="_blank">
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
           
            <div class="user--info">

                        <div class="notification" id="notif-icon">
                                    <i class='bx bx-bell'   ></i>
                                    <span class="num">8</span>

                        </div>
               
                        <div class="user-profile">

                            <?php if (!empty($profileImagePath)): ?>
                                <img id="profile-icon"  class="profile-icon" src="<?php echo $profileImagePath; ?>" alt="User Profile Image">
                            <?php else: ?>
                                <img id="profile-icon"  class="profile-icon" src="images/PROFILE1.png" alt="Default Profile Image">
                            <?php endif; ?>


                        </div>
                        
                        <div class="dropdown-profile">

                            <div class="sub-menu">

                                <div class="user-info">
                                            <?php if (!empty($profileImagePath)): ?>
                                                <img   class="usermain-profile" src="<?php echo $profileImagePath; ?>" alt="User Profile Image">
                                            <?php else: ?>
                                                <img   class="usermain-profile" src="images/PROFILE1.png" alt="Default Profile Image">
                                            <?php endif; ?>
                                        <p><?php echo $username; ?></p>
                                    </div>


                                    <a href="profile.php" class="edit-profile">
                                        <div class="edit-profile1">
                                            <i class='bx bxs-user-circle'></i>
                                            <p>Edit Profile</p>
                                        </div>
                                        <i class='bx bx-chevron-right'></i>
                                    </a>

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



        <div id="swup" class="first-container">
            <h1>Hi, <?php echo $firstName; ?>.</h1>

            <p>Dashboard </p>

            <div class="inside-container">

                    <div class="info-available">

                        
        
                                <!-- Slider container -->
                        <div class="slider"  >


                                        <div class="slide">
                                            <img src="./images/announcements/1.png" />
                                        </div>

                                        <div class="slide">
                                            <img src="./images/announcements/2.png" />
                                        </div>


                                        <div class="slide">
                                            <img src="./images/announcements/3.png" />
                                        </div>
                          
                                    <!-- Control buttons -->
                                    <button class="btn btn-next"> > </button>
                                    <button class="btn btn-prev">
                                    < </button>

        </div>




                        <div class="bottom-container">
                            <p>Common Services</p>

                            <div class="conditions-slider">
                                <a href="./pdf/baby-vaccine.pdf" class="box">
                                    <img src="images/conditions-icon/condition-1.png" alt="">
                                    <p>Baby Vaccines</p>
                                </a>

                                <a href="./pdf/ANIMAL.pdf" class="box">
                                    <img src="images/conditions-icon/condition-2.png" alt="">
                                    <p>Animal Bite</p>
                                    </a>

                                <a href="./pdf/ANIMAL.pdf" class="box">
                                    <img src="images/conditions-icon/condition-3.png" alt="">
                                    <p>Animal Vaccine</p>
                                    </a>

                                <a href="./pdf/FLU.pdf" class="box">
                                    <img src="images/conditions-icon/condition-4.png" alt="">
                                    <p>Flu Vaccine</p>
                                    </a>

                                <a href="./pdf/mental-help.pdf" class="box">
                                    <img src="images/conditions-icon/condition-5.png" alt="">
                                    <p>Mental Health</p>
                                    </a>

                                <a href="./pdf/famplan.pdf" class="box">
                                    <img src="images/conditions-icon/condition-6.png" alt="">
                                    <p>Family Planning</p>
                                    </a>
                            </div>
                        </div>
                    
        
        
                    </div>

        

                <div class="calendar-container">

                    <div class="calendar-month-arrow-container">
                        <div class="calendar-month-year-container">
                        <select class="calendar-years"></select>
                        <select class="calendar-months">
                        </select>
                        </div>

                        <div class="calendar-month-year">
                        </div>

                        <div class="calendar-arrow-container">
                        <button class="calendar-today-button"></button>
                        <button class="calendar-left-arrow">
                            ← </button>
                        <button class="calendar-right-arrow"> →</button>
                        </div>
                    
                    </div>

                            <ul class="calendar-week">
                            </ul>
                            <ul class="calendar-days">
                            </ul>
                            
                    </div>





                    






                
        

            </div>


            <div id="calendar-container" class="calendar-container">
                <div id="calendar-header" class="calendar-header">
                    <span id="calendar-month-year" class="calendar-month-year"></span>
                </div>
                <div id="calendar-dates" class="calendar-dates">
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



    <script> 


                const weekArray = ["Sun", "Mon", "Tue", "Wed", "Thr", "Fri", "Sat"];
                const monthArray = [
                "January",
                "February",
                "March",
                "April",
                "May",
                "June",
                "July",
                "August",
                "September",
                "October",
                "November",
                "December"
                ];
                const current = new Date();
                const todaysDate = current.getDate();
                const currentYear = current.getFullYear();
                const currentMonth = current.getMonth();

                window.onload = function () {
                const currentDate = new Date();
                generateCalendarDays(currentDate);

                let calendarWeek = document.getElementsByClassName("calendar-week")[0];
                let calendarTodayButton = document.getElementsByClassName(
                    "calendar-today-button"
                )[0];
                calendarTodayButton.textContent = `Today ${todaysDate}`;

                calendarTodayButton.addEventListener("click", () => {
                    generateCalendarDays(currentDate);
                });

                weekArray.forEach((week) => {
                    let li = document.createElement("li");
                    li.textContent = week;
                    li.classList.add("calendar-week-day");
                    calendarWeek.appendChild(li);
                });

                const calendarMonths = document.getElementsByClassName("calendar-months")[0];
                const calendarYears = document.getElementsByClassName("calendar-years")[0];
                const monthYear = document.getElementsByClassName("calendar-month-year")[0];

                const selectedMonth = parseInt(monthYear.getAttribute("data-month") || 0);
                const selectedYear = parseInt(monthYear.getAttribute("data-year") || 0);

                monthArray.forEach((month, index) => {
                    let option = document.createElement("option");
                    option.textContent = month;
                    option.value = index;
                    option.selected = index === selectedMonth;
                    calendarMonths.appendChild(option);
                });

                const currentYear = new Date().getFullYear();
                const startYear = currentYear - 30;
                const endYear = currentYear + 30;
                let newYear = startYear;
                while (newYear <= endYear) {
                    let option = document.createElement("option");
                    option.textContent = newYear;
                    option.value = newYear;
                    option.selected = newYear === selectedYear;
                    calendarYears.appendChild(option);
                    newYear++;
                }

                const leftArrow = document.getElementsByClassName("calendar-left-arrow")[0];

                leftArrow.addEventListener("click", () => {
                    const monthYear = document.getElementsByClassName("calendar-month-year")[0];
                    const month = parseInt(monthYear.getAttribute("data-month") || 0);
                    const year = parseInt(monthYear.getAttribute("data-year") || 0);

                    let newMonth = month === 0 ? 11 : month - 1;
                    let newYear = month === 0 ? year - 1 : year;
                    let newDate = new Date(newYear, newMonth, 1);
                    generateCalendarDays(newDate);
                });

                const rightArrow = document.getElementsByClassName("calendar-right-arrow")[0];

                rightArrow.addEventListener("click", () => {
                    const monthYear = document.getElementsByClassName("calendar-month-year")[0];
                    const month = parseInt(monthYear.getAttribute("data-month") || 0);
                    const year = parseInt(monthYear.getAttribute("data-year") || 0);
                    let newMonth = month + 1;
                    newMonth = newMonth === 12 ? 0 : newMonth;
                    let newYear = newMonth === 0 ? year + 1 : year;
                    let newDate = new Date(newYear, newMonth, 1);
                    generateCalendarDays(newDate);
                });

                calendarMonths.addEventListener("change", function () {
                    let newDate = new Date(calendarYears.value, calendarMonths.value, 1);
                    generateCalendarDays(newDate);
                });

                calendarYears.addEventListener("change", function () {
                    let newDate = new Date(calendarYears.value, calendarMonths.value, 1);
                    generateCalendarDays(newDate);
                });
                };

                function generateCalendarDays(currentDate) {
                const newDate = new Date(currentDate);
                const year = newDate.getFullYear();
                const month = newDate.getMonth();
                const totalDaysInMonth = getTotalDaysInAMonth(year, month);
                const firstDayOfWeek = getFirstDayOfWeek(year, month);
                let calendarDays = document.getElementsByClassName("calendar-days")[0];

                removeAllChildren(calendarDays);

                let firstDay = 1;
                while (firstDay <= firstDayOfWeek) {
                    let li = document.createElement("li");
                    li.classList.add("calendar-day");
                    calendarDays.appendChild(li);
                    firstDay++;
                }

                let day = 1;
                while (day <= totalDaysInMonth) {
                    let li = document.createElement("li");
                    li.textContent = day;
                    li.classList.add("calendar-day");
                    if (todaysDate === day && currentMonth === month && currentYear === year) {
                    li.classList.add("calendar-day-active");
                    }
                    calendarDays.appendChild(li);
                    day++;
                }

                const monthYear = document.getElementsByClassName("calendar-month-year")[0];
                monthYear.setAttribute("data-month", month);
                monthYear.setAttribute("data-year", year);
                const calendarMonths = document.getElementsByClassName("calendar-months")[0];
                const calendarYears = document.getElementsByClassName("calendar-years")[0];
                calendarMonths.value = month;
                calendarYears.value = year;
                }

                function getTotalDaysInAMonth(year, month) {
                return new Date(year, month + 1, 0).getDate();
                }

                function getFirstDayOfWeek(year, month) {
                return new Date(year, month, 1).getDay();
                }

                function removeAllChildren(parent) {
                while (parent.firstChild) {
                    parent.removeChild(parent.firstChild);
                }
                }





</script>



<script>

"use strict";
// Select all slides
const slides = document.querySelectorAll(".slide");

// loop through slides and set each slides translateX
slides.forEach((slide, indx) => {
  slide.style.transform = `translateX(${indx * 100}%)`;
});

// select next slide button
const nextSlide = document.querySelector(".btn-next");

// current slide counter
let curSlide = 0;
// maximum number of slides
let maxSlide = slides.length - 1;

// add event listener and navigation functionality
nextSlide.addEventListener("click", function () {
  // check if current slide is the last and reset current slide
  if (curSlide === maxSlide) {
    curSlide = 0;
  } else {
    curSlide++;
  }

  //   move slide by -100%
  slides.forEach((slide, indx) => {
    slide.style.transform = `translateX(${100 * (indx - curSlide)}%)`;
  });
});

// select next slide button
const prevSlide = document.querySelector(".btn-prev");

// add event listener and navigation functionality
prevSlide.addEventListener("click", function () {
  // check if current slide is the first and reset current slide to last
  if (curSlide === 0) {
    curSlide = maxSlide;
  } else {
    curSlide--;
  }

  //   move slide by 100%
  slides.forEach((slide, indx) => {
    slide.style.transform = `translateX(${100 * (indx - curSlide)}%)`;
  });
});





</script>



 
</body>
</html>
