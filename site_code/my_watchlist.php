<?php include_once("header.php") ?>
<?php require("utilities.php");
require("debug.php");
require("mysql_connect.php"); ?>

<!-- This Page will display the users current watchlist items + current bid states for those items.
- It will also have a recommendation feature to recommend items similar to previously bought items
 that a customer might want to add to their watchlist.
 - User will also be able to see interesting stats on the side related. -->

<div class="container mb-5">
    <div class="row d-flex justify-content-between" id="topstats">
        <div class="col-sm p-3">
            <div class="col-md-12 border-bottom border-info">
                <h4># Live items you're watching</h4>
                <div class="row">
                    <div class="col push-left">
                        <i class="fa fa-clock-o fa-2x" aria-hidden="true"></i>
                    </div>
                    <div class="col push-right">7 Items</div>
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
        <div class="col-3">
            <h2>Notifications</h2>
            Notification messages for bid updates go here!
        </div>

    </div>

</div>