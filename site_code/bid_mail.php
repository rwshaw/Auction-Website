<?php require_once("../mysql_connect.php");
require_once("debug.php");
require_once("utilities.php");
?> 


<?php

function email_results() {



    //get all listing ids
    $query = "SELECT listingID, reservePrice, sellerUserID FROM auction_listing";
    $listings = SQLQuery($query);

    // if condition, e.g if endtime in the last 5 minutes

    //loop over each listing (could add already emailed value here)
    foreach ($listings as $listing) {
    //first check if there were any bids over the reserve
        $listing_id = $listing['listingID'];
        $reserve_price = $listing['reservePrice'];
        $sellerUserID = $listing['sellerUserID'];

        $maxprice = SQLQuery("SELECT max(bidPrice) as maxbidprice FROM bids WHERE listingID='$listing_id'");
        
        if ($maxprice[0]['maxbidprice'] == null || $maxprice[0]['maxbidprice'] <= $reserve_price) {
            //emailsellernotsold($sellerUserID);
            error_log("no_reserve");
            error_log($listing_id);
        }
        else {
            //emailsellersold($sellerUserID,$maxprice);
            $query = "SELECT max(bidPrice) as usermax,userID FROM bids WHERE (listingID=$listing_id) group by userID";
            $ordered_bids = SQLQuery($query);
           
            foreach ($ordered_bids as $bid) {
                $userID = $bid['userID'];
                $usermax = $bid['usermax'];

                if ($usermax == $maxprice[0]['maxbidprice']) {
                    //emailwinner();
                    error_log("winner");
                    error_log($listing_id);
                } else {

                    //emailloser();
                    error_log("loser");
                    error_log($listing_id);
                }
            }



        }


        
    }



}

email_results();



?> 