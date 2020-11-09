<?php 
  error_reporting(E_ALL);

  ini_set('display_errors', '1'); 

  require_once('mysql_connect.php'); 

  //dummy variable for now, delete later
  $email = "tom.cruise@ourauctionsite.com"; 

  // open connection to auctionsite database
  $db = OpenDbConnection(); 
  $check_user_query = "SELECT * FROM users WHERE email = '$email' LIMIT 1"; 
  $user = SQLQuery($check_user_query); 
  $email1 = $user['email'];
  echo $email1;

  CloseDbConnection($db)
  // 2 session variables should be displayed: 
    // user id
    // logged_in  

?>
