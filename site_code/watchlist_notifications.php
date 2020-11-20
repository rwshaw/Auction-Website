<?php require_once("mysql_connect.php");
require_once("debug.php");
require_once("utilities.php");
?> 
 

 <?php

    // HANDLES BID UPDATE NOTIFICATIONS FOR USER ON WATCHLIST OF ITEM.

    if (!isset($_POST['functionname']) || !isset($_POST['arguments'])) {
        return;
    }

    // Extract arguments from the POST variables:
    $item_id = implode('', $_POST['arguments']);

    // GET User passed from ajax
    $userid = implode('', $_POST['username']);

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

        $success = send_user_email($userid, $subject, $message);

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

        $success = send_user_email($userid, $subject, $message);

        echo $success;
    } elseif ($_POST['functionname'] == "bid_notification") {
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
        $prev_bid_query1 = "select @rownum:=@rownum+1 as row_num, b.bidID 
                        from bids b, (SELECT @rownum:=-1) r 
                        where listingID = $item_id order by bidID asc"; //issue with lab bid!!!
        $prev_bid_search = SQLQuery($prev_bid_query1);
        $max_row_num = end($prev_bid_search);
        $max_row_num_result = $max_row_num["row_num"]; // if this is 0 - then no previous high bidder.
        // save max_row_num to variable
        // now find the user associated with the previous bidID to find previous highest bidder
        if ($max_row_num_result == 0) {
            $prev_high_bidder = null;
        } else {
            $lag_row_num = $max_row_num_result - 1;
            $prev_high_bidder_query = "SELECT userID from bids where bidID= (select bidID
                                    from (select @rownum:=@rownum+1 as row_num, b.bidID 
                                                            from bids b, (SELECT @rownum:=-1) r 
                                                            where listingID = 91 order by bidID asc) a
                                                            where a.row_num = $lag_row_num)";
            $prev_high_result = SQLQuery($prev_high_bidder_query);
            $prev_high_bidder = $prev_high_result[0]["userID"];
        }

        // For each watcher in list, we need to assess and generate message based on $latest_bid row and whether they are buyer/seller, bidder or lag bidder.
        foreach ($watchers as $row) {
            if ($row["userID"] === $latest_bid[0]["bidder"]) {
                $message = "You just bid &#163;" . $latest_bid[0]["bidPrice"] . " for '" . $latest_bid[0]["itemName"] . "'";
            } elseif ($row["userID"] === $latest_bid[0]["sellerID"]) {
                $message = $latest_bid[0]["bidder_name"] . " just bid &#163;" . $latest_bid[0]["bidPrice"] . " for your item '" . $latest_bid[0]["itemName"] . "'";
            } elseif ($row["userID"] === $prev_high_bidder) {
                $message = "You were just outbid! " . $latest_bid[0]["bidder_name"] . " just bid &#163;" . $latest_bid[0]["bidPrice"] . " for '" . $latest_bid[0]["itemName"] . "'";
            } else {
                $message = $latest_bid[0]["bidder_name"] . " just bid &#163;" . $latest_bid[0]["bidPrice"] . " for '" . $latest_bid[0]["itemName"] . "'";
            } // Now send email + perform insert 
            //EMAIL
            $subject = "Watchlist Update";
            $html_message = "<html><p>" . $message . "</p></html>";
            $email_sent = (send_user_email($row["userID"], $subject, $html_message) == TRUE) ? TRUE : FALSE; //evaluating if email successfullly sent to populate emailSent field in notifications.
            $notification_insert = "INSERT INTO watch_notifications(watchID, bidID, message, emailSent) VALUES (" . $row["watchID"] . "," . $latest_bidID . ",\"" . $message . "\"," . $email_sent . ")";
            // execute insert
            $conn = OpenDbConnection(); // Open connection ready for insert
            if ($conn->query($notification_insert) === TRUE) {
                $res = "success";
            } else {
                $res = "Error: " . $sql . $conn->error;
            }
        }
        echo $res;
    } elseif ($_POST['functionname'] == "update_bids") {
        $item_array = $_POST['arguments'];
        //  echo $item_array;
        // for each element in array, we need to create a table for 5 last bids.
        // if the bid is from this user, add "YOU" tag to the end.
        // if no bids at all for that item yet -> NO BIDS YET, if bid is empty but no_bids > 0 , then leave html empty but keep 5 rows.
        //return key value array - key =item id, value = html for table rows.
        //  echo $item_array;

        $return_array = array();

        foreach ($item_array as $item_id) {
            $recent_bids_query = "SELECT userID, bidPrice,date_format(bidTimestamp, '%d/%m %H:%i') as bidTimestamp from bids where listingID = $item_id
                                order by bidID desc
                                LIMIT 5";
            $con = OpenDbConnection();
            $recent_bids = $con->query($recent_bids_query);

            // if there are results , now loop through result and create html table
            $html_rows = "";    // initialise html to insert into for each row.
            $winning_badge = '<span class="badge badge-secondary">WINNING</span>'; //badges to make things clearer.
            $you_badge = '<span class="badge badge-primary">YOU</span>';


            if ($recent_bids->num_rows > 0) {
                for ($i = 0; $i < 5; $i++) {
                    if (mysqli_data_seek($recent_bids, $i)) { // if there are rows from DB query left rprocess to html
                        $bid = mysqli_fetch_row($recent_bids);
                        if ($i == 0) {
                            if ($bid[0] == $userid) {
                                $html_rows .= '<tr class="bg-success"><td style="text-align:center">' . $bid[1] . $winning_badge . $you_badge . '<small>    ' . $bid[2] . '</small></td></tr>';
                            } else {
                                // winning but not YOU
                                $html_rows .= '<tr class="bg-success"><td style="text-align:center">' . $bid[1] . $winning_badge . '<small>    ' . $bid[2] . '</small></td></tr>';
                            }
                        } else {
                            // not winning bid, but row in query 
                            if ($bid[0] == $userid) {
                                $html_rows .= '<tr class="bg-danger"><td style="text-align:center">' . $bid[1] . $you_badge . '<small>    ' . $bid[2] . '</small></td></tr>';
                            } else {
                                // not winning but not YOU
                                $html_rows .= '<tr><td style="text-align:center">' . $bid[1] . '<small>    ' . $bid[2] . '</small></td></tr>';
                            }
                        }
                    } else {
                        $html_rows .= '<tr><td style="text-align:center">-- No older bid --</td></tr>';
                    }
                }
            } else {
                $html_rows = '<tr><td style="text-align:center">No bids yet</td></tr><tr><td></td></tr><tr><td></td></tr><tr><td></td></tr><tr><td></td></tr>';
            }
            // put key and html value in return array
            $return_array[$item_id] = $html_rows;
        }

        echo json_encode($return_array);
    }


    ?>