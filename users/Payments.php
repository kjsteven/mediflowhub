<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments</title>


    <link rel="icon" href="images/logo.png" type="image/png">

    <link rel="stylesheet" type="text/css" href="style/payments.css">
    <link rel="stylesheet" href="style/transitions.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">

</head>
<body>



<div class="main--content">

<i class='bx bx-arrow-back'></i>

        <div class="first-container">
                        
    
    

                <div class="container1">

            
                                        
                    <h2>Choose your payment method</h2> 

                        <div class="payments">





                            <div class="paymentContainer">
                                            
                                            <label>
                                                <input type="radio" name="paymentMethod" value="Over the Counter">
                                                <div class="Description"><p>Over the counter</p>
                                                <span>process payments by using cash over-the-counter at physical clinic branches, as well as by cash and payment cards using ATMs.</span></div>
                                                
                                            </label>
                                            <label>
                                                <input type="radio" name="paymentMethod" value="GCash"> 
                                                <div class="Description"><p>Gcash</p>
                                                <span>process payments by using cash over-the-counter at physical clinic branches, as well as by cash and payment cards using ATMs.</span></div>
                                            </label>
                                            <label>
                                                <input type="radio" name="paymentMethod" value="Credit Card">
                                                <div class="Description"><p>Credit Card</p>
                                                <span>process payments by using cash over-the-counter at physical clinic branches, as well as by cash and payment cards using ATMs.</span></div>
                                            </label>
        
        
                            </div>
        
                            <div class="divider"></div>
        
        
        
                            <div class="form-container">
                                    <h2>Credit Card Information</h2>

                                        <div class="form-field">
                                        <label for="cardNumber">CARD NUMBER:</label>
                                        <input type="text" id="cardNumber" name="cardNumber" placeholder="Enter Card Number">
                                        </div>

                                        <div class="form-field">
                                        <label for="nameOnCard">NAME ON CARD:</label>
                                        <input type="text" id="nameOnCard" name="nameOnCard" placeholder="Enter Name on Card">
                                        </div>

                                        <div class="form-field">
                                        <label for="expirationDate">EXPIRATION DATE:</label> 
                                        <input type="text" id="expirationDate" name="expirationDate" class="expiration-date" placeholder="MM">
                                        <input type="text" id="expirationYear" name="expirationYear" class="expiration-date" placeholder="YYYY">
                                        </div>
        
        
                            </div>
        
        



                            <div class="confirm-button">
                                <button>Confirm Button</button>
                            </div>



                        </div>
                        






                </div>


                <div class="container2">

                
                     

                                        
                        <h2>Summary</h2> 

            
                    <div class="payments2">
                
                
                            <div class="Doctorscontainer">
                                                        
                                                    
                                        <div class="doctor-field">

                                                <div class="profile-doc">
                                                        <button></button>
                                                        <div class="name-doc">

                                                                <p>Dr. Names</p>
                                                                <span>General Medicine</span>

                                                        </div>
                                                        

                                                 </div>



                                                <div class="time-field">
                                                        <div class="time-doc">
                                                                <p>Time</p>
                                                                <span>09:00 am</span>
                                                        </div>

                                                        <div class="time-doc">
                                                                <p>Date</p>
                                                                <span>16 Feb 2023</span>
                                                        </div>
    
                                                </div>
                                                
                                        </div>


                                        <div class="notes-field">
                                                <p>Notes</p>
                                                <p>  </p>

                                        </div>



            
                
                
                            </div>


                            <div class="doctors-fee">
                                <h4>DOCTOR'S FEE: </h4>
                                <p>Online Consultation</p>
                                <p>200.00 PHP</p>
                            </div>

                            <div class="Pharmacy-fee">
                                <h4>PHARMACY: </h4>
                                <p>Mucosolvan 24hrs 75mg Capsule  Capsule<br>(₱ 232.50 PHP)</p>
                                <P>Neurotain Capsule - 20s <br> (₱ 1,260.00 PHP)</P>
                                <h4>TOTAL: ₱ 1,492.5</h4>
                            </div>





                            

                    </div>
                
        





                </div>





        </div>

</div>
   



<script src="script/script.js"></script>

</body>
</html>

