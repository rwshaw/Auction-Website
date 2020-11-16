<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

// include header
include_once("header.php"); 

// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {
  header("location: browse.php"); 
	exit; 
}

// initialise variables
$email = " "; 
$password = " "; 

// extract $_POST variables
if (isset($_POST['login_user'])) {

  // import sql connect page
  require_once('mysql_connect.php'); 

  // open connection to database
  $db = OpenDbConnection(); 

  // escape strings from post request
  $email = $db->real_escape_string($_POST['login_email']); 
  $password = $db->real_escape_string($_POST['login_password']); 

  // check user exists in database and retieve email and password
    // prepare and bind parameters
  $user_information_query = $db->prepare("SELECT * FROM users WHERE email=?"); 
    // bind parameters
  $user_information_query->bind_param("s", $email); 
    // execute prepared statement with binded parameters
  $user_information_query->execute(); 
    // retrieve result
  $result = $user_information_query->get_result();
    // fetch as associative array
  $user = $result->fetch_assoc(); 

    // check user with this email exists
  if ($user) {

      // retrieve email, user_id and password for user 
    $user_email = $user['email'];
    $hashed_user_password = $user['password']; 
    $user_id = $user['userID'];

      // check passwords match 
    if (password_verify($password, $hashed_user_password)) {

      // create session variables 
      if (session_id() == "") {
        session_start();
      }
      $_SESSION['logged_in'] = true;
      $_SESSION['username'] = $user_id;
      $_SESSION['account_type'] = "buyer";

      // redirect 
      echo "You are now logged in! You will be redirected shortly.";
      header("refresh:3;url=index.php");
    }

    else { // if email exists, but password is incorrect
      echo "login unsuccesful. Wrong email, password combination. Please try again. You will be redirected shortly. "; 
      header("refresh:5;url=index.php");
    }

  } 

  else { // if email does not exist in database
    echo "login unsuccesful. Wrong email, password combination. Please try again. You will be redirected shortly. "; 
    header("refresh:5;url=index.php");
  }
  
  // close database connection
  CloseDbConnection($db); 

}

// // TODO: Extract $_POST variables, check they're OK, and attempt to login.
// // Notify user of success/failure and redirect/give navigation options.

?> 

<!-- Footer -->
<?php include_once("footer.php")?>