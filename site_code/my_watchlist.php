<?php include_once("header.php") ?>
<?php require_once("utilities.php");
require_once("debug.php");
require_once("mysql_connect.php"); ?>

<!-- This Page will display the users current watchlist items + current bid states for those items.
- It will also have a recommendation feature to recommend items similar to previously bought items
 that a customer might want to add to their watchlist.
 - User will also be able to see interesting stats on the side related. -->

<?php
//get variables for html insertion.
$userid = 4; //user 4 = Qasim - testing  (should get this from session later. //$_SESSION['userID'];

//Items you're watching

$count_watched_query = "select count(watchID) as no_watched from watchlist w left join auction_listing using(listingID) where w.userID =$userid and isWatching = 1 and endTime>now()";
$count_watched = SQLQuery($count_watched_query);
$count_watched_result = $count_watched[0]["no_watched"];

//Live Bids
//TODO


//Auctions won past 24 hours
// TODO



//Items you're watching
//Need Item details + last 3 bids


// Past 50 notifications
$show_notif_query = "select message, wl.listingID, b.bidTimestamp from watch_notifications w 
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
        $html =  "<strong class=\"mr-auto\">AuctionXpress</strong>
        <small class=\"text-muted\">" . $row["bidTimestamp"] . "</small>
        <div class=\"alert alert-info alert-dismissible fade show\" role=\"alert\">
            <strong>Watchlist Update!</strong><a href=\"listing.php?item_id=" . $row["listingID"] .
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
                <h4># Live bids</h4>
                <div class="row">
                    <div class="col push-left">
                        <i class="fa fa-money fa-2x" aria-hidden="true"></i>
                    </div>
                    <div class="col push-right">7 Bids</div>
                </div>

            </div>
        </div>
        <div class="col-sm p-3">
            <div class="col-md-12 border-bottom border-info">
                <h4>Auctions won past 24 hours.</h4>
                <div class="row">
                    <div class="col push-left">
                        <i class="fa fa-shopping-cart fa-2x" aria-hidden="true"></i>
                    </div>
                    <div class="col push-right">7 Auctions</div>
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
            <div class="list-group-item ">
                <div class="row justify-content-start align-items-start p-2">
                    <div class="col-8">Item + Item details as link</div>
                    <div class="col-4" id="scroll">Bids + bid time. Last 10 bids. Winning bid highlighted in Green at top., customer bid highlighted in blue.
                    </div>
                </div>
            </div>
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