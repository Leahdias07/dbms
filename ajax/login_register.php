<?php 

require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');


date_default_timezone_set("Asia/Kolkata");


if (isset($_POST['register'])) {
  $data = filteration($_POST);

  // Match password and confirm password field
  if ($data['pass'] != $data['cpass']) {
      echo 'pass_mismatch';
      exit;
  }

  // Check user exists or not
  $u_exist = select("SELECT * FROM `user_cred` WHERE `email` = ? OR `phonenum` = ? LIMIT 1",
      [$data['email'], $data['phonenum']], "ss");

  if (mysqli_num_rows($u_exist) != 0) {
      $u_exist_fetch = mysqli_fetch_assoc($u_exist);
      echo ($u_exist_fetch['email'] == $data['email']) ? 'email_already' : 'phone_already';
      exit;
  }

  // Upload user image to server
  $img = uploadUserImage($_FILES['profile']);

  if ($img == 'inv_img') {
      echo 'inv_img';
      exit;
  } else if ($img == 'upd_failed') {
      echo 'upd_failed';
      exit;
  }

  // Store the plain password (consider hashing this)
  $plain_pass = $data['pass'];

  // Insert user data into the database
  $query = "INSERT INTO `user_cred`(`name`, `email`, `address`, `phonenum`, `pincode`, `dob`, `profile`, `password`, `is_verified`, `security_question`, `security_answer`) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
  $values = [$data['name'], $data['email'], $data['address'], $data['phonenum'], $data['pincode'], $data['dob'], $img, $plain_pass, '1', $data['security_question'], $data['security_answer']];

  // Debugging: Print values
  print_r($values);

  if (insert($query, $values, 'sssssssssss')) {
      echo 1; // Success
  } else {
      echo 'Insertion failed'; // More generic error message
  }
}

if (isset($_POST['login'])) {
    $data = filteration($_POST);

    $u_exist = select("SELECT * FROM `user_cred` WHERE `email`=? OR `phonenum`=? LIMIT 1",
    [$data['email_mob'], $data['email_mob']], "ss");

    if (mysqli_num_rows($u_exist) == 0) {
        echo 'inv_email_mob';
    } else {
        $u_fetch = mysqli_fetch_assoc($u_exist);
        if ($u_fetch['is_verified'] == 0) {
            echo 'not_verified';
        } else if ($u_fetch['status'] == 0) {
            echo 'inactive';
        } else {
            // Compare plain password
            if ($data['pass'] != $u_fetch['password']) {
                echo 'invalid_pass';
            } else {
                session_start();
                $_SESSION['login'] = true;
                $_SESSION['uId'] = $u_fetch['id'];
                $_SESSION['uName'] = $u_fetch['name'];
                $_SESSION['uPic'] = $u_fetch['profile'];
                $_SESSION['uPhone'] = $u_fetch['phonenum'];
                echo 1;
            }
        }
    }
}

if (isset($_POST['forgot_pass'])) {
    $data = filteration($_POST);
  
    $u_exist = select("SELECT * FROM user_cred WHERE email=? LIMIT 1", [$data['email']], "s");
  
    if (mysqli_num_rows($u_exist) == 0) {
      echo 'inv_email'; // Email does not exist
    } else {
      $u_fetch = mysqli_fetch_assoc($u_exist);
      echo $u_fetch['security_question']; // Return the security question
    }
  }
  
  if (isset($_POST['verify_security'])) {
    $data = filteration($_POST);
  
    $u_exist = select("SELECT * FROM user_cred WHERE email=? LIMIT 1", [$data['email']], "s");
  
    if (mysqli_num_rows($u_exist) == 0) {
      echo 'inv_email'; // Email does not exist
    } else {
      $u_fetch = mysqli_fetch_assoc($u_exist);
      if ($u_fetch['security_answer'] != $data['security_answer']) {
        echo 'invalid_answer'; // Security answer does not match
      } else {
        echo 'valid_answer'; // Correct answer
      }
    }
  }
  
  if (isset($_POST['set_new_password'])) {
    $data = filteration($_POST);
  
    if ($data['new_password'] != $data['confirm_new_password']) {
      echo 'pass_mismatch'; // Passwords do not match
    } else {
      $query = "UPDATE user_cred SET password=? WHERE email=?";
      $values = [$data['new_password'], $data['email']];
      if (update($query, $values, 'ss')) {
        echo 'password_updated'; // Password updated successfully
      } else {
        echo 'upd_failed'; // Update failed
      }
    }
  }
?>