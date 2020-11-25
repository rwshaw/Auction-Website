<?php include_once("header.php") ?>
<?php require_once("utilities.php");
require_once("debug.php");
require_once("../mysql_connect.php");
require_once("watchlist_notifications.php");
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL); ?>

<!-- This Page will display the users current watchlist items + current bid states for those items.
- It will also have a recommendation feature to recommend items similar to previously bought items
 that a customer might want to add to their watchlist.
 - User will also be able to see interesting stats on the side related. -->


<?php

//get variables for html insertion.
$userid = $_SESSION['user_id'];

//Items you're watching

$count_watched_query = "select count(watchID) as no_watched from watchlist w left join auction_listing using(listingID) where w.userID =$userid and isWatching = 1 and endTime>now()";
$count_watched = SQLQuery($count_watched_query);
$count_watched_result = $count_watched[0]["no_watched"];

//Live Winning Bids
$num_winning_query = "SELECT count(*) as num_winning
                        from 
                        (SELECT max(b.bidID) as winning_bid_id, b.listingID FROM `bids` b
                        inner join auction_listing al 
                        on al.listingID = b.listingID
                        and al.endTime > now()
                        group by listingID) a
                        inner join bids b2
                        on b2.bidID = a.winning_bid_id
                        and b2.userID = $userid";
$num_winning = SQLQuery($num_winning_query);
$num_winning_result = $num_winning[0]["num_winning"];


//Auctions won past week
$num_wins_query = "SELECT count(*) as num_wins
                    from 
                    (SELECT max(b.bidID) as winning_bid_id, b.listingID FROM `bids` b
                    inner join auction_listing al 
                    on al.listingID = b.listingID
                    and al.endTime < now()
                    and al.endTime >= date_sub(now(), interval 7 day)
                    group by listingID) a
                    inner join bids b2
                    on b2.bidID = a.winning_bid_id
                    and b2.userID = $userid";
$num_wins = SQLQuery($num_wins_query);
$num_wins_result = $num_wins[0]["num_wins"];


//Items you're watching
//Need Item details + last 3 bids
// get items that are being watched. Via AJAX script we will request bid details for that item.
$watched_items_query = "SELECT a.listingID, ItemName, ItemDescription, ifnull(max(bidPrice),startPrice) as currentPrice, count(bidID) as num_bids, endTime, c.deptName, c.subCategoryName 
                        from auction_listing a
                        left join bids b on a.listingID = b.listingID 
                        left join categories c on a.categoryID = c.categoryID 
                        inner join watchlist w 
                        on w.listingID = a.listingID
                        and w.userID = $userid
                        where endTime > now() 
                        group by a.listingID, a.ItemName, a.ItemDescription, a.endTime, c.deptName, c.subCategoryName
                        order by endTime asc";
$watched_items = SQLQuery($watched_items_query);
$watched_items_array = array(); // create empty array to store itemID, to pass to bidupdate function in script.



// Past 50 notifications
$show_notif_query = "SELECT message, wl.listingID, b.bidTimestamp from watch_notifications w 
                        inner join watchlist wl
                        on w.watchID = wl.watchID
                        and wl.userID = $userid
                        left join bids b
                        on b.bidID = w.bidID
                        order by bidTimestamp DESC
                        LIMIT 50";
$show_notif_result = SQLQuery($show_notif_query);

//define function to print notification in correct format
function print_notifs($notif_array)
{
    foreach ($notif_array as $row) {
        $html =  "<div class=\"alert alert-info alert-dismissible fade show\" role=\"alert\">
        <small class=\"text-muted\">" . $row["bidTimestamp"] . "</small><br><strong>Watchlist Update!</strong><a href=\"listing.php?item_id=" . $row["listingID"] .
            "\"> " . $row["message"] . "</a>
             <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
                <span aria-hidden=\"true\">&times;</span>
            </button>
        </div>";
        echo $html;
    }
}

?>

<div class="container mb-5">
    <div class="row d-flex justify-content-between" id="topstats">
        <div class="col-sm p-3">
            <div class="col-md-12 border-bottom border-info">
                <h4># Live items you're watching</h4>
                <div class="row">
                    <div class="col push-left">
                        <i class="fa fa-clock-o fa-2x" aria-hidden="true"></i>
                    </div>
                    <div class="col push-right"><?php echo $count_watched_result . " Items" ?></div>
                </div>

            </div>
        </div>
        <div class="col-sm p-3">
            <div class="col-md-12 border-bottom border-info">
                <h4># Live winning bids</h4>
                <div class="row">
                    <div class="col push-left">
                        <i class="fa fa-money fa-2x" aria-hidden="true"></i>
                    </div>
                    <div class="col push-right"><?php echo $num_winning_result . " Bids" ?></div>
                </div>

            </div>
        </div>
        <div class="col-sm p-3">
            <div class="col-md-12 border-bottom border-info">
                <h4>Auctions won past week</h4>
                <div class="row">
                    <div class="col push-left">
                        <i class="fa fa-shopping-cart fa-2x" aria-hidden="true"></i>
                    </div>
                    <div class="col push-right"><?php echo $num_wins_result . " Auctions" ?></div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-8">
            <h2>Items you're watching</h2>
            <!-- print one item per watchlist -->
            <?php
            // print watchlist, if no items in watchlist, display error alert.
            if ($watched_items != false) {
                foreach ($watched_items as $item) {
                    print_watchlist_listing($item["listingID"], $item["ItemName"], $item["ItemDescription"], $item["currentPrice"], $item["num_bids"], $item["endTime"], $item["deptName"], $item["subCategoryName"]);
                    // add item_id to array to be able to update bids in real-time.
                    array_push($watched_items_array, $item["listingID"]);
                }
            } else {
                // If no results returned - print alert
                $header = "Oooops...";
                $text1 = "Looks like you don&#39;t  have any live auctions in your watchlist.";
                $text2 = "Keep shopping to find things to add! ";
                print_alert("warning", $header, $text1, $text2);
            }

            ?>
        </div>
        <div class="col-4">
            <h2>Notifications</h2>
            Notification messages for bid updates go here!

            <div class="p-3">
                <?php
                //  insert notifs here
                print_notifs($show_notif_result);
                ?>


            </div>





            <!--  -->
        </div>

    </div>

</div>



<script>
    // JavaScript functions: updateBids

    // update the bid tables for all items in watchlist.

    $(document).ready(function() {
        updateBids();
        setInterval(updateBids, 3000);
    });

    function updateBids() {
        $.ajax("watchlist_notifications.php", {
            type: "POST",
            data: {
                functionname: 'update_bids',
                arguments: [<?php echo implode(',', $watched_items_array); ?>],
                username: [<?php echo ($_SESSION['user_id']); ?>]
            },

            success: function(item_html, success) {
                // console.log("returned success");
                // console.log(item_html);
                parsed_html = JSON.parse(item_html);
                // console.log(parsed_html);
                // in returned array, change item html where id matches, to the html for that id (item_id)
                for (var key in parsed_html) {
                    $("#".concat(key)).html(parsed_html[key]);

                }
            },
            error: function(obj, textstatus) {
                console.log("Error");
            }
        });
    }
</script>