<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

if (isset($_POST['login_user'])) {
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $password = mysqli_real_escape_string($db, $_POST['password']);

  if (empty($username)) {
  	array_push($errors, "Username is required");
  }
  if (empty($password)) {
  	array_push($errors, "Password is required");
  }

  if (count($errors) == 0) {
  	$password = md5($password);
  	$query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
  	$results = mysqli_query($db, $query);
  	if (mysqli_num_rows($results) == 1) {
  	  $_SESSION['username'] = $username;
  	  $_SESSION['success'] = "You are now logged in";
  	  header('location: index.php');
  	}else {
  		array_push($errors, "Wrong username/password combination");
  	}
  }
}

?>


  	// // attempt to login by checking inputted email and password match user in database 
  	// // hash password 
  	// $hashed_password = password_hash($password, PASSWORD_DEFAULT);
  	// $check_user_exists_query = "SELCT * FROM users WHERE email='$email' AND password='$hashed_password'"; 
  	// $result = mysqli_query($db, $check_user_exists_query) or die(mysqli_error($db); 
  	// if($result) { // check user exists
  	// 	echo $result; 
  	// }



//   		if (mysqli_num_rows($result) == 1) { // check only one user xeists 
//   			$_SESSION['logged_in'] = true;
//   			$_SESSION['username'] = "test";
//   			$_SESSION['account_type'] = "buyer";
//   			echo('<div class="text-center">You are now logged in! You will be redirected shortly.</div>');
// 			header("refresh:5;url=index.php"); // Redirect to index after 5 seconds
//   		} 
//   		else {
//   			echo "login unsuccesful. Wrong email, password combination. Please try again"; 
//   			header("refresh:5;url=index.php");
//   		} -->






