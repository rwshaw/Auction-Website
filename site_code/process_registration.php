<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start(); 

// initialise db connection 
// change this later to use mysql_connect.php
// for now, remember to close connection 
$db = new mysqli('localhost','website','3ZqpGsAsmC6U2opZ', 'auctionsite');

// register user 
if (isset($_POST['reg_user'])) {
// extract $_POST variables
  $email 		  		        = $_POST['email'];
  $password 	  		      = $_POST['password'];
  $password_confirmation  = $_POST['password_confirmation'];
  $fname 		  		        = $_POST['FirstName'];
  $lname			            = $_POST['LastName'];
  $addressLine1	          = $_POST['AddressLine1']; 
  $addressLine2  		      = $_POST['AddressLine2'];
  $city			  		        = $_POST['City'];
  $postcode  			        = $_POST['PostCode'];

  // check user has provided input for required fields 
  // Also add in trim() function to remove whitespace?
  if(empty($fname)) {echo "Please enter your first name.";}
  if(empty($lname)) {echo "Please enter your last name.";}
  if(empty($addressLine1)) {echo "Please enter your first address line.";}
  if(empty($city)) {echo "Please enter your city.";}
  if(empty($postcode)) {echo "Please enter your postcode.";}
  if(empty($email)) {echo "Please enter your email.";}
  if(empty($password)) {echo "Please enter your password.";}
  if(empty($password_confirmation)) {echo "Please enter your password again.";}

  // check passwords match
  if($password != $password_confirmation) {echo "passwords do not match.";}

  // check a user does not already exist with same email address
  $check_user_query = "SELECT * FROM users WHERE email = '$email' LIMIT 1"; 
  $result = mysqli_query($db,$check_user_query) or die( mysqli_error($db)); 
  $user = mysqli_fetch_array($result);

  if ($user) {// check if user exists 
    if ($user['email'] === $email) { // check if user with same email already exists
      echo "email already exsits";
    }
  }

  // https://www.php.net/manual/en/faq.passwords.php
  // need to change password to varchar(60) to ensure hashed passwords can fit
  $hashed_password = password_hash($password, PASSWORD_DEFAULT);

  // Create new account by inserting user into database
  $insert_new_user = "INSERT INTO users (fName, lName, email, password, addressLine1, addressLine2, city, postcode) VALUES('$fname', '$lname','$email', '$hashed_password', '$addressLine1', '$addressLine2', '$city', '$postcode')";
  mysqli_query($db, $insert_new_user);
  $result = mysqli_query($db,$insert_new_user);

  // notify user of success
  // Declare function that sanitizes and validates inputted email address 
  function sanitize_my_email($email) {
      $sanitized_email = filter_var($email, FILTER_SANITIZE_EMAIL);
      if (filter_var($sanitized_email, FILTER_VALIDATE_EMAIL)) {
          return true;
      } else {
          return false;
      }
  }
  // declare variables for PHP mailer 
  $user_email = $email;
  $subject = 'Congratulations! You are registered.';
  $message = 'Copy / verify your email by clicking the link below:';
  $headers = 'From: noreply@auctionexpress.com';
  // check if email address is valid 
  $check_email = sanitize_my_email($user_email);
  if ($check_email == false) {
      echo "email incorrect";
  } 
  else { //send email 
      mail($user_email, $subject, $message, $headers);
      echo "email sent";
  }

// redirect user 
  header("Location: browse.php"); 
  $_SESSION['logged_in'] = true;

// TODO: Extract $_POST variables, check they're OK, and attempt to create
// an account. Notify user of success/failure and redirect/give navigation 
// options.

}
?>