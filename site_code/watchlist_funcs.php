<?php require_once("../mysql_connect.php");
require_once("debug.php");
require_once("utilities.php");
?> 
 

 <?php

  // FUNCTIONS TO ADD TO AND REMOVE FROM WATCHLIST FOR USER.

  if (!isset($_POST['functionname']) || !isset($_POST['arguments'])) {
    return;
  }

  // Extract arguments from the POST variables:
  $item_id = implode('', $_POST['arguments']);

  // GET session userID
  $userid = implode('', $_POST['username']);

  // ADD TO WATCHLIST, IF SUCCESS SEND EMAIL
  if ($_POST['functionname'] == "add_to_watchlist") {
    // TODO: Update database and return success/failure.
    // takes item ID - adds to watchlist for that customer.
    $check_query = "SELECT isWatching FROM watchlist where userID = $userid and listingID=$item_id";
    $result = SQLQuery($check_query);
    $current_value = $result[0]["isWatching"];

    $conn = OpenDbConnection(); // Open connection ready for insert/update

    //if True, user is already watching.
    if ($current_value === 1) {
      $res = "success";

      // TODO - currently do nothing, but maybe this should return message back to listings page "already watching this item"?

    } elseif (is_null($current_value)) {
      //if DB has isWatching as null i.e. no record, we should insert the record
      $insert_stmt = "INSERT INTO watchlist (listingID, userID, isWatching) VALUES ($item_id, $userid, TRUE)";
      if ($conn->query($insert_stmt) === TRUE) {
        $res = "success";
      } else {
        $res = "Error: " . $sql . $conn->error;
      }
    } else {
      // Value is false and needs to be updated to true (e.g. if customer added, then removed item from watchlist).
      $update_stmt = "UPDATE watchlist set isWatching = TRUE where userID = $userid and listingID = $item_id";
      if ($conn->query($update_stmt) === TRUE) {
        $res = "success";
      } else {
        $res = "Error: " . $sql . $conn->error;
      }
    }

    // Send email now handled in main php file. On success of this function, ajax function will call another function to send the email.

 
  } // REMOVE FROM WATCHLIST IF SUCCESS, SEND EMAIL.
  elseif ($_POST['functionname'] == "remove_from_watchlist") {
    // TODO: Update database and return success/failure.
    $check_query = "SELECT isWatching FROM watchlist where userID = $userid and listingID=$item_id";
    $result = SQLQuery($check_query);
    $current_value = $result[0]["isWatching"];

    $conn = OpenDbConnection(); // open connection for update of isWatching from TRUE to FALSE (1 -> 0).

    //if TRUE then update to false, return success
    if ($current_value == 1) {
      $update_stmt = "UPDATE watchlist set isWatching = FALSE where userID = $userid and listingID = $item_id";
      if ($conn->query($update_stmt) === TRUE) {
        $res = "success";
      } else {
        $res = "Error: " . $sql . $conn->error;
      }
    } else {
      // if Value is false or null then user already not watching
      $res = $res;
      //Do nothing for now. Other value could be returned to say user already not watching.
    }

    // Send email now handled in main php file. On success of this function, ajax function will call another function to send the email.


  }

  CloseDbConnection($conn);

  // Note: Echoing from this PHP function will return the value as a string.
  // If multiple echo's in this file exist, they will concatenate together,
  // so be careful. You can also return JSON objects (in string form) using
  // echo json_encode($res).
  echo $res;

?>
