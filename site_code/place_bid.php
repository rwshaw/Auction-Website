<?php 
require_once("../mysql_connect.php");
require_once("debug.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>


<?php
session_start();



if (!isset($_POST['functionname']) || !isset($_POST['arguments'])) {
    return;
  }
  
  // Extract arguments from the POST and session variables:
 
  $results = $_POST['arguments']; 

  
  if ($_POST['functionname'] == "place_bid")  {
    //Temporary until session variables called

    if (array_key_exists('logged_in',$_SESSION) == false  || $_SESSION['logged_in'] == false) {
      $res = "login";

    } else {
      $signedin = $_SESSION['logged_in'];
      $user_id = $_SESSION['user_id'];

      $item_id = $results[0];
      $bidprice = $results[1];
      $startprice = $results[2];

      error_log($user_id);
      $datetime = new DateTime();
      $timestamp= $datetime->format('Y-m-d H:i:s');
      //retrieve current price
      $con = OpenDbConnection();
      $stmt = $con->prepare($maxprice = "SELECT MAX(bidPrice) as currentPrice FROM bids WHERE listingID=?");
      $stmt->bind_param("i",$item_id);
      $stmt->execute();
      $get_mysql = $stmt->get_result();
      $current_price = $get_mysql->fetch_assoc();
      $current_price = $current_price['currentPrice'];
      $current_price=(number_format($current_price, 2));
      $stmt->close();
      CloseDbConnection($con);

      if (($current_price < $bidprice) && ($startprice < $bidprice)) {
        //prep statement to enter bid data if greater than current price
        $con = OpenDbConnection();
        $stmt = $con->prepare("INSERT INTO bids (userID,listingID,bidPrice,bidTimestamp) VALUES (?,?,?,'$timestamp')");
        $stmt->bind_param("iid",$user_id,$item_id,$bidprice);
        $stmt->execute();
        $stmt->close();
        $res = "bidplaced";
        CloseDbConnection($con);
      }  
      else {
        $res = "bidfailed";
      }
        
    }

    

    echo $res;
   
    
  }
  
  
  // error_log("neet");
  // Note: Echoing from this PHP function will return the value as a string.
  // If multiple echo's in this file exist, they will concatenate together,
  // so be careful. You can also return JSON objects (in string form) using
  // echo json_encode($res).
  
  
?>

