<?php 

  require('admin/inc/db_config.php');
  require('admin/inc/essentials.php');

  date_default_timezone_set("Asia/Kolkata");

  session_start();

  // Redirect to index if user is not logged in
  if(!(isset($_SESSION['login']) && $_SESSION['login'] == true)){
    redirect('index.php');
  }

  if(isset($_POST['booking_completion']))
  {
    // Insert payment data into the database without using any payment gateway

    $ORDER_ID = 'ORD_'.$_SESSION['uId'].random_int(11111,9999999);    
    $CUST_ID = $_SESSION['uId'];
    $TXN_AMOUNT = $_SESSION['room']['payment'];

    $frm_data = filteration($_POST);

    // Insert into booking_order table
  $query = "INSERT INTO `booking_order`(`user_id`, `room_id`, `check_in`, `check_out`, `booking_status`, `order_id`, `arrival`) 
        VALUES (?,?,?,?,'booked',?,0)";

  insert($query, [
      $_SESSION['uId'],
      $_SESSION['room']['id'],
      $frm_data['checkin'],
      $frm_data['checkout'],
      $ORDER_ID
  ], 'iisss');
    
    $booking_id = mysqli_insert_id($con);

    // Insert into booking_details table
    $query2 = "INSERT INTO `booking_details`(`booking_id`, `room_name`, `price`, `total_pay`, 
                `user_name`, `phonenum`, `address`) 
               VALUES (?,?,?,?,?,?,?)";

    insert($query2, [
      $booking_id,
      $_SESSION['room']['name'],
      $_SESSION['room']['price'],
      $TXN_AMOUNT,
      $frm_data['name'],
      $frm_data['phonenum'],
      $frm_data['address']
    ], 'issssss');

    // Clear room session data after booking confirmation
    unset($_SESSION['room']);
  }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <style>
        /* Background and text styling */
        body {
            font-family: Arial, sans-serif;
            background: url('hotel-background.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Container styling */
        .confirmation-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 40px 60px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        /* Heading and text styling */
        h1 {
            color: #4CAF50;
            font-size: 2em;
            margin: 0;
        }
        
        p {
            font-size: 1.2em;
            margin: 10px 0;
        }

        /* Button styling */
        .button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 1em;
            color: #fff;
            background-color: #4CAF50;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <div class="confirmation-container">
        <h1>Booking Confirmed!</h1>
        <p>Your booking was successful!</p>
        <p>Thank you for choosing us. We look forward to welcoming you.</p>
        <a href="index.php" class="button">Return to Home</a>
    </div>

</body>
</html>
