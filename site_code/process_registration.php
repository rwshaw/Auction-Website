<?php 
  error_reporting(E_ALL);

  ini_set('display_errors', '1'); 

  require_once('mysql_connect.php'); 

  
  $db = OpenDbConnection(); 

  $check_user_query = "SELECT * FROM users WHERE email = '$email' LIMIT 1"; 
  $result = mysqli_query($db,$check_user_query) or die( mysqli_error($db)); 
  $user = mysqli_fetch_assoc($result);
  $email = $user['email']; 
  echo $email; 


?>
