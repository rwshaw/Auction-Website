<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {
	header("location: browse.php"); 
	exit; 
}

// initialise variables
$email = " "; 
$password = " "; 

// initialise db connection 
// change this later to use mysql_connect.php
// remember to close connection
$db = new mysqli('localhost','website','3ZqpGsAsmC6U2opZ', 'auctionsite');

// 
if (isset($_POST['login_user'])) {
	// Extract post variables  
  	$email = mysqli_real_escape_string($db, $_POST['login_email']);
  	$password = mysqli_real_escape_string($db, $_POST['login_password']);

	// check user inputted post variables 
  	if (empty(trim($email))) {echo "Please enter your email.";}
  	if (empty(trim($password))) {echo "Please enter your password.";}

	// attempt to login by checking inputted email and password match user in database
	// user email verificaiton first 
	$check_user_email_exists = "SELECT email FROM users WHERE email = '$email'"; // Prepare a select statement to check email 
	$result = mysqli_query($db, $check_user_email_exists) or die(mysqli_error($db)); 
  	$number_of_rows = mysqli_num_rows($result); 
  	if ($number_of_rows == 1) {   	// check only 1 user with this email exists. 
  		$retrieve_user_password = "SELECT password FROM users WHERE email = '$email'"; // prepare sql statement to retrieve user's (hashed) password
  		$hashed_password_result = mysqli_query($db, $retrieve_user_password) or die(mysqli_error($db)); 
  		$hashed_password_array = mysqli_fetch_assoc($hashed_password_result); 
  		$hashed_password = $hashed_password_array["password"]; 
  		// password verification 
  		if (password_verify($password,$hashed_password)) { // check inputted password matches password in db 
  			session_start(); // start session and initialise variables
  			$_SESSION['logged_in'] = true;
  			$_SESSION['username'] = "test";
  			$_SESSION['account_type'] = "buyer";
   			echo('<div class="text-center">You are now logged in! You will be redirected shortly.</div>');
 			header("refresh:5;url=index.php"); // Redirect to index after 5 seconds
   		} 
   		else {
   			echo "login unsuccesful. Wrong email, password combination. Please try again"; 
   			header("refresh:5;url=index.php");
  		}
  	}
}

// TODO: Extract $_POST variables, check they're OK, and attempt to login.
// Notify user of success/failure and redirect/give navigation options.

?> 