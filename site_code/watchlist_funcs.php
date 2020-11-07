<?php 
require("mysql_connect.php");
require("debug.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>


 <?php

if (!isset($_POST['functionname']) || !isset($_POST['arguments'])) {
  return;
}

// Extract arguments from the POST variables:
$item_id = implode($_POST['arguments']); 

if ($_POST['functionname'] == "add_to_watchlist") {
  // TODO: Update database and return success/failure.
	// To do for Q: load $session userID and pass for the query and poss some default vals for the other fields
	$connection = OpenDbConnection(INSERTSESSIONVARSandREMOVEFUNCHARDCODING);
	error_log(error_log( print_r($connection, TRUE) ));
	$query = "INSERT INTO watchlist (userID, listingID, bidID)"."VALUES (INSERTSESSIONVAR,$item_id,INSERTSESSIONVAR)";
	$result = mysqli_query($connection,$query) or die('Error making saveToDatabase query');
	CloseDbConnection($connection);
	$res = "success";
}
else if ($_POST['functionname'] == "remove_from_watchlist") {
  // TODO: Update database and return success/failure.

  $res = "success";
}

// Note: Echoing from this PHP function will return the value as a string.
// If multiple echo's in this file exist, they will concatenate together,
// so be careful. You can also return JSON objects (in string form) using
// echo json_encode($res).
echo $res;

?>
