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
$item_id = implode('',$_POST['arguments']); 

if ($_POST['functionname'] == "updateprice") {
  // TODO: Update database and return success/failure.
	// To do for Q: load $session userID and pass for the query and poss some default vals for the other fields

    $maxprice = "SELECT MAX(bidPrice) as currentPrice FROM bids WHERE listingID='$item_id'";
    $current_price = SQLQuery($maxprice);
    $current_price = $current_price['currentPrice'];
    $res=(number_format($current_price, 2));
    //error_log("yeet");
    
}


// error_log("neet");
// Note: Echoing from this PHP function will return the value as a string.
// If multiple echo's in this file exist, they will concatenate together,
// so be careful. You can also return JSON objects (in string form) using
// echo json_encode($res).
echo $res;

?>