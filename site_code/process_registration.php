<?php 
  error_reporting(E_ALL);
  ini_set('display_errors', '1');

  require_once("mysql_connect.php");

  session_start(); 

  // initialise database connection
  $db = OpenDbConnection(); 

  //  
  $check_user_query = "SELECT * FROM users WHERE email = '$email' LIMIT 1"; 
  $result = mysqli_query($db,$check_user_query) or die( mysqli_error($db));

  // close database connection
  mysqli_free_result($result); 


?>
