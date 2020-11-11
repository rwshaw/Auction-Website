<?php require_once("mysql_connect.php");
require_once("debug.php");
require_once("utilities.php");
?> 
 

 <?php


if (!isset($_POST['functionname']) || !isset($_POST['arguments'])) {
  return;
}

// Extract arguments from the POST variables:
$item_id = implode('',$_POST['arguments']);

// GET session userID
$userid =  4; //user 4 = Qasim - testing  (should get this from session later. //$_SESSION['userID'];

//  SEND EMAIL for successful removal from watchlist
if ($_POST['functionname'] == "remove_watch_email") {
    // Send email of successful removal from watchlist
    // get item name
    $item_name_query = "SELECT itemName FROM auction_listing WHERE listingID=$item_id";
    $item_result = SQLQuery($item_name_query);
    $itemName = $item_result[0]["itemName"];

    // TODO create subject + html message.
    $subject = "Item removed from watchlist";

    $message = "<html>
    <h2>That's all done!</h2>
    <p><span style=\"color: #008000;\">$itemName</span> has been <span style=\"color: #008000;\">successfullly removed from your watchlist</span>.</p>
    <p>To see items you currently have in your watchlist, go to the 'My Watchlist' tab from the navigation bar.</p>
    <p><em>Happy Buying!</em></p>
    <p><em>The AuctionXpress Team</em></p></html>";

    $success = send_user_email($userid,$subject,$message);

    echo $success;

} elseif ($_POST['functionname'] == "add_watch_email") {
    // Send email of successfull add to watchlist
   // get item name
    $item_name_query = "SELECT itemName FROM auction_listing WHERE listingID=$item_id";
    $item_result = SQLQuery($item_name_query);
    $itemName = $item_result[0]["itemName"];

    // TODO create subject + html message.
    $subject = "New item added to watchlist";

    $message = "<html>
    <h2>Great News!</h2>
    <p><span style=\"color: #008000;\">$itemName</span> has been <span style=\"color: #008000;\">successfullly added to your watchlist</span>.</p>
    <p>To see items you currently have in your watchlist, go to the 'My Watchlist' tab from the navigation bar.</p>
    <p><em>Happy Buying!</em></p>
    <p><em>The AuctionXpress Team</em></p></html>";

    $success = send_user_email($userid,$subject,$message);

    echo $success;
}
 elseif ($_POST['functionname'] == "bid_notification") {
    //  takes in listingID, finds latest bid inserted, adds notifications for all users on watchlist.
    // USER NOTIFICATIONS FOR BIDS
    // if bid is successful - notifiaction for all watchlist users
    // bidID, listingID
    // for all custs who are watching this listingID: 
    // if user == bidder then 'You',
    //  if user == seller then 'Your item..', 
    //  otherwise, if lag(bidID) == user, then "another user just outbid you, 
    //  otherwise "firstname" just bid on ItemNAme.

    //Start - execute queries to get relevant info to compare in order to build notification message for user.
    


 }

?>