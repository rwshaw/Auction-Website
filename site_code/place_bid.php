<?php 
require_once("mysql_connect.php");
require_once("debug.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>


<?php

if (!isset($_POST['functionname']) || !isset($_POST['arguments'])) {
    return;
  }
  
  // Extract arguments from the POST variables:
  $results = $_POST['arguments']; 
  
  if ($_POST['functionname'] == "place_bid") {
    //Temporary until session variables called
    $user_id = 2;

    $item_id = $results[0];
    $bidprice = $results[1];
    error_log(gettype($user_id));
    $res = "success";
    $datetime = new DateTime();
    $timestamp= $datetime->format('Y-m-d H:i:s');
    error_log($timestamp);
    $con = OpenDbConnection();
    $stmt = $con->prepare("INSERT INTO bids (userID,listingID,bidPrice,bidTimestamp) VALUES (?,?,?,'$timestamp')");
    $stmt->bind_param("iid",$user_id,$item_id,$bidprice);
    $stmt->execute();
    $stmt->close();
    CloseDbConnection($con);
      
  }
  
  
  // error_log("neet");
  // Note: Echoing from this PHP function will return the value as a string.
  // If multiple echo's in this file exist, they will concatenate together,
  // so be careful. You can also return JSON objects (in string form) using
  // echo json_encode($res).
  echo $res;
  
?>

