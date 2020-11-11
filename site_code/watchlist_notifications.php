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
$userid =  2; //user 4 = Qasim - testing  (should get this from session later. //$_SESSION['userID'];

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
    $watchers_query = "Select userID, watchID from watchlist w
                        where w.listingID =$item_id";

    // get latest bid info, by listing and bidder that placed the bid
    $latest_bid_query = "select bidID, b.userID as bidder, u.fName as bidder_name, b.listingID, sellerUserID as sellerID, a.itemName, bidPrice, bidTimestamp
                            from bids b
                            left join auction_listing a
                            using(listingID)
                            left join users u
                            using(userID)
                            where bidID = (select max(bidID) as bidID from bids where listingID = $item_id and userID=$userid )";

    // get results
    $watchers = SQLQuery($watchers_query);
    $latest_bid = SQLQuery($latest_bid_query);
    $latest_bidID = $latest_bid[0]["bidID"];
    
    // get bid ID to work out prev higest bidder
    // Since no lag or window functions in MySQL 5 - had to do some googling to be able to execute desired query in one query. Query was informed by this link - https://stackoverflow.com/questions/7100902/php-mysql-how-can-i-use-set-rank-0-in-query
    $prev_bid_query1 = "select @rownum:=@rownum+1 as lag_bid, b.bidID 
                        from bids b, (SELECT @rownum:=-1) r 
                        where listingID = $item_id order by bidID asc"; //issue with lab bid!!!
    $prev_bid_search = SQLQuery($prev_bid_query1);
    $lag_bid = end($prev_bid_search);
    $lag_bid_result = $lag_bid["lag_bid"]; // if this is 0 - then no previous high bidder.
    // save lag_bid to variable
    // now find the user associated with that bidID to find previous highest bidder
    if ($lag_bid_result == 0) {
        $prev_high_bidder = null;
    } else {
        $prev_high_bidder_query = "SELECT userID from bids where bidID= $lag_bid_result";
        $prev_high_result = SQLQuery($prev_high_bidder_query);
        $prev_high_bidder = $prev_high_result[0]["userID"];
    }

    // For each watcher in list, we need to assess and generate message based on $latest_bid row and whether they are buyer/seller, bidder or lag bidder.
    foreach ($watchers as $row) {
        if ($row["userID"] === $latest_bid[0]["bidder"]) {
            $message = "You just bid £" . $latest_bid[0]["bidPrice"] . " for '" . $latest_bid[0]["itemName"] . "'";
        } elseif ($row["userID"] === $latest_bid[0]["sellerID"]) {
            $message = $latest_bid[0]["bidder_name"] . " just bid £" . $latest_bid[0]["bidPrice"] . " for your item '" . $latest_bid[0]["itemName"] . "'";
        } elseif ($row["userID"] === $prev_high_bidder) {
            $message = "You were just outbid! " . $latest_bid[0]["bidder_name"] . " just bid £" . $latest_bid[0]["bidPrice"] . " for '" . $latest_bid[0]["itemName"] . "'";
        } else {
            $message = $latest_bid[0]["bidder_name"] . " just bid £" . $latest_bid[0]["bidPrice"] . " for '" . $latest_bid[0]["itemName"] . "'";
        } // Now send email + perform insert 
        //EMAIL
        $subject = "Watchlist Update";
        $html_message = "<html><p>" . $message . "</p></html>";
        $email_sent = (send_user_email($row["userID"],$subject,$html_message) == TRUE) ? TRUE : FALSE ; //evaluating if email successfullly sent to populate emailSent field in notifications.
        $notification_insert = "INSERT INTO watch_notifications(watchID, bidID, message, emailSent) VALUES (" . $row["watchID"] . "," . $latest_bidID . ",\"" . $message . "\"," . $email_sent . ")";
        // execute insert
        $conn = OpenDbConnection(); // Open connection ready for insert
        if ($conn->query($notification_insert) === TRUE) {
            $res = "success";
          } else {
            $res = "Error: " .$sql . $conn->error;
          }

      }
      echo $lag_bid_result;
 }

?>