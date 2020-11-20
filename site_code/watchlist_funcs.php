<?php require_once("mysql_connect.php");
require_once("debug.php");
require_once("utilities.php");
?> 
 

 <?php

// FUNCTIONS TO ADD TO AND REMOVE FROM WATCHLIST FOR USER.

if (!isset($_POST['functionname']) || !isset($_POST['arguments'])) {
  return;
}

// Extract arguments from the POST variables:
$item_id = implode('',$_POST['arguments']);

// GET session userID
$userid = implode('',$_POST['username']);

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
      $res = "Error: " .$sql . $conn->error;
    }
  } else { 
    // Value is false and needs to be updated to true (e.g. if customer added, then removed item from watchlist).
    $update_stmt = "UPDATE watchlist set isWatching = TRUE where userID = $userid and listingID = $item_id";
    if ($conn->query($update_stmt) === TRUE) {
      $res = "success";
    } else {
      $res = "Error: " .$sql . $conn->error;
    }
  }

  // if ($res === "success") {
  //   // Send email of successfull add to watchlist
  //   // get item name
  //   $item_name_query = "SELECT itemName FROM auction_listing WHERE listingID=$item_id";
  //   $item_result = SQLQuery($item_name_query);
  //   $itemName = $item_result[0]["itemName"];

  //   // TODO create subject + html message.
  //   $subject = "New item added to watchlist";

  //   $message = "<html>
  //   <h2>Great News!</h2>
  //   <p><span style=\"color: #008000;\">$itemName</span> has been <span style=\"color: #008000;\">successfullly added to your watchlist</span>.</p>
  //   <p>To see items you currently have in your watchlist, go to the 'My Watchlist' tab from the navigation bar.</p>
  //   <p><em>Happy Buying!</em></p>
  //   <p><em>The AuctionXpress Team</em></p></html>";

  //   send_user_email($userid,$subject,$message);


  // }
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
      $res = "Error: " .$sql . $conn->error;
    }
  } else {
    // if Value is false or null then user already not watching
    $res = $res;
    //Do nothing for now. Other value could be returned to say user already not watching.
  }

  // if ($res === "success") {
  //   // Send email of successful removal from watchlist
  //   // get item name
  //   $item_name_query = "SELECT itemName FROM auction_listing WHERE listingID=$item_id";
  //   $item_result = SQLQuery($item_name_query);
  //   $itemName = $item_result[0]["itemName"];

  //   // TODO create subject + html message.
  //   $subject = "Item removed from watchlist";

  //   $message = "<html>
  //   <h2>That's all done!</h2>
  //   <p><span style=\"color: #008000;\">$itemName</span> has been <span style=\"color: #008000;\">successfullly removed from your watchlist</span>.</p>
  //   <p>To see items you currently have in your watchlist, go to the 'My Watchlist' tab from the navigation bar.</p>
  //   <p><em>Happy Buying!</em></p>
  //   <p><em>The AuctionXpress Team</em></p></html>";

  //   send_user_email($userid,$subject,$message);
  // }


}

CloseDbConnection($conn);

// Note: Echoing from this PHP function will return the value as a string.
// If multiple echo's in this file exist, they will concatenate together,
// so be careful. You can also return JSON objects (in string form) using
// echo json_encode($res).
echo $res;

?>
