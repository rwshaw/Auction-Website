<?php include_once("header.php")?>
<?php require("utilities.php")?>

<div class="container">

<h2 class="my-3">Recommendations for you</h2>

<?php

  error_reporting(E_ALL);
  ini_set('display_errors', '1');

  // import sql connection script
  require_once('mysql_connect.php'); 

  // open connection to database
  $db = OpenDbConnection(); 

  // Check if the user is already logged in, if yes then redirect him to welcome page
  if(!isset($_SESSION["logged_in"]) && !$_SESSION["logged_in"] === true) {
    header("location: browse.php"); 
    exit; 
  }


  // Retrieve all bids the user has made 
    // get user id of logged-in user 
  $user_id = '1';
  echo "user_id: " . $user_id . "<br><br>";
  // $user_id = $_SESSION['user_id']; 
  // echo $_SESSION['user_id']; 

  // retrieve all users
    // prepare query 
  $all_users_query = $db->prepare("SELECT userID FROM users"); 
    // execute prepared statement 
  $all_users_query->execute();
    // retrieve result
  $all_users = $all_users_query->get_result(); 
    // fetch results and store in array
  $users = array();
  foreach ($users as $row) {
    $users[] = $row; 
  }

  print_r($users);


  // $bidders = array();

  // while ($users = $all_users->fetch_array(MYSQLI_ASSOC)) {
  //   foreach ($users as $rows) {
  //     $bidders[] = $rows;
  //     echo $rows;
  //   }
  // }

  // echo "<pre>",  print_r($bidders), "</pre>";

  // while ($users = $all_users->fetch_array(MYSQLI_NUM)) {
  //   for ($i = 0; $i < count($users); $i++) {
  //     if($i != $user_id) {
  //       print_r($users[$i]);
  //     }
  //   }
    

  // //   // print_r($users); echo "<br>";


  // //   // foreach ($users as $user) {
  // //   //   echo $user . "<br>";
  // //   // }
  // }

  // $user_ids = array();
  // while ($users = $all_users->fetch_array()) {
  //   $user_ids[] = $users;
  // }
  // echo "<pre>", var_dump($user_ids), "</pre>";


  
?>
