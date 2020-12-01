<?php 
require_once("../mysql_connect.php");
require_once("debug.php");
require_once("utilities.php");
?> 


<?php

function email_results() {


    //get all listing ids  -- could shange this to select endtimes just in the last minute and run every minute
    $query = "SELECT listingID, reservePrice, sellerUserID FROM auction_listing WHERE (endTime between date_sub(now(), interval 1 DAY) and now()) and (resultsEmailed=FALSE)";
    $listings = SQLQuery($query);

    // if condition, e.g if endtime in the last 5 minutes

    //loop over each listing (could add already emailed value here)
    if ($listings == false){
        //do nothing
        //this will occur if no auction has ended that hasnt been emailed
    }
    else {
        foreach ($listings as $listing) {
        //first check if there were any bids over the reserve
            $listing_id = $listing['listingID'];
            $reserve_price = $listing['reservePrice'];
            $sellerUserID = $listing['sellerUserID'];
            $con = OpenDbConnection();
            $query = "UPDATE auction_listing SET resultsEmailed=TRUE WHERE listingID=$listing_id";
            if ($con->query($query) === TRUE) {
                $res = "success";
                } else {
                    error_log("Error: ". $con->error);
                }
            $maxprice = SQLQuery("SELECT max(bidPrice) as maxbidprice FROM bids WHERE listingID='$listing_id'");
            $maxprice=$maxprice[0]['maxbidprice'];
            if ($maxprice == null || $maxprice <= $reserve_price) {
                emailsellernotsold($sellerUserID,$listing_id,$maxprice);
                echo "Item: $listing_id <br>";
                echo "notsold <br>";
            }
            else {
                emailsellersold($sellerUserID,$listing_id,$maxprice);
                $query = "SELECT max(bidPrice) as usermax,userID FROM bids WHERE (listingID=$listing_id) group by userID";
                $ordered_bids = SQLQuery($query);
                echo "Item: $listing_id <br>";
                echo "sold <br>";
            
                foreach ($ordered_bids as $bid) {
                    $userID = $bid['userID'];
                    $usermax = $bid['usermax'];

                    if ($usermax == $maxprice) {
                        emailwinner($listing_id,$maxprice, $userID);
                        echo "winner <br>";
                    } else {
                        emailloser($listing_id,$maxprice, $userID);
                        echo "loser <br>";
                    }
                }
            }  
        }
    }
}

function emailsellernotsold($sellerUserID,$listing_id) {
    $item_name_query = "SELECT itemName FROM auction_listing WHERE listingID=$listing_id";
    $item_result = SQLQuery($item_name_query);
    $itemName = $item_result[0]["itemName"];

    // TODO create subject + html message.
    $subject = "Bidding has ended on your listing";

    $message = "<html>
    <h2>Hi there!</h2>
    <p><span style=\"color: #008000;\">$itemName</span>  <span style=\"color: #008000;\">has not been sold.</span></p>
    <p>Unfortunately your auction has not met the reserve price and has not been sold. Better luck nextime!</p>
    <p><em>Happy Selling!</em></p>
    <p><em>The AuctionXpress Team</em></p></html>";

    send_user_email($sellerUserID, $subject, $message);
}

function emailsellersold($sellerUserID,$listing_id,$maxprice) {
    $item_name_query = "SELECT itemName FROM auction_listing WHERE listingID=$listing_id";
    $item_result = SQLQuery($item_name_query);
    $itemName = $item_result[0]["itemName"];

    // TODO create subject + html message.
    $subject = "Bidding has ended on your listing";

    $message = "<html>
    <h2>Hi there!</h2>
    <p><span style=\"color: #008000;\">$itemName</span> <span style=\"color: #008000;\">has been sold for &#163;$maxprice!</span></p>
    <p>Congratulations on your succcessful sale!</p>
    <p><em>Happy Selling!</em></p>
    <p><em>The AuctionXpress Team</em></p></html>";

    send_user_email($sellerUserID, $subject, $message);
}

function emailwinner($listing_id,$maxprice, $userID) {
    $item_name_query = "SELECT itemName FROM auction_listing WHERE listingID=$listing_id";
    $item_result = SQLQuery($item_name_query);
    $itemName = $item_result[0]["itemName"];

    // TODO create subject + html message.
    $subject = "Congratulations you won!";

    $message = "<html>
    <h2>Hi there!</h2>
    <p>You have won <span style=\"color: #008000;\">$itemName</span>  <span style=\"color: #008000;\"> for &#163;$maxprice!</span></p>
    <p>Congratulations on your auction win!</p>
    <p><em>Happy bidding!</em></p>
    <p><em>The AuctionXpress Team</em></p></html>";

    send_user_email($userID, $subject, $message);
}
function emailloser($listing_id,$maxprice, $userID) {
    $item_name_query = "SELECT itemName FROM auction_listing WHERE listingID=$listing_id";
    $item_result = SQLQuery($item_name_query);
    $itemName = $item_result[0]["itemName"];

    // TODO create subject + html message.
    $subject = "Bidding has ended";

    $message = "<html>
    <h2>Hi there!</h2>
    <p>Your bid on <span style=\"color: #008000;\">$itemName</span>  <span style=\"color: #008000;\"> has been unsuccessful. </span></p>
    <p>The item the sold at &#163;$maxprice. Better luck next time!</p>
    <p><em>Happy bidding!</em></p>
    <p><em>The AuctionXpress Team</em></p></html>";

    send_user_email($userID, $subject, $message);
}


email_results();



?> 