<?php include_once("header.php") ?>
<?php
require("utilities.php");
require_once("../mysql_connect.php");
require("debug.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<?php
// For now, index.php just redirects to browse.php, but you can change this
// if you like.

// header("Location: browse.php");


?>

<div class="row">
  <div class="col-md-12">
    <div class="carousel slide" id="carousel">
      <ol class="carousel-indicators">
        <li data-slide-to="0" data-target="#carousel">
        </li>
        <li data-slide-to="1" data-target="#carousel" class="active">
        </li>
        <li data-slide-to="2" data-target="#carousel">
        </li>
      </ol>
      <div class="carousel-inner">
        <div class="carousel-item" style="height: 350px;">
          <img class="d-block w-100" style="margin: 0; position: absolute; top: 50%; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%);" src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1589&q=80" />
        </div>
        <div class="carousel-caption">
          <h4>
            Welcome to AUCTIONXPRESS.
          </h4>
        </div>
        <div class="carousel-item active" style="height: 350px;">
          <img class="d-block w-100" style="margin: 0; position: absolute; top: 50%; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%);" src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1500&q=80" />
        </div>
        <div class="carousel-caption">
          <h4>
            Welcome to AUCTIONXPRESS.
          </h4>
        </div>
        <div class="carousel-item" style="height: 350px;">
          <img class="d-block w-100" style="margin: 0; position: absolute; top: 50%; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%);" src="https://images.unsplash.com/photo-1497515098781-e965764ab601?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1608&q=80" />
        </div>
        <div class="carousel-caption">
          <h4>
            Welcome to AUCTIONXPRESS.
          </h4>
        </div>
      </div> <a class="carousel-control-prev" href="#carousel" data-slide="prev"><span class="carousel-control-prev-icon"></span> <span class="sr-only">Previous</span></a> <a class="carousel-control-next" href="#carousel" data-slide="next"><span class="carousel-control-next-icon"></span> <span class="sr-only">Next</span></a>
    </div>
  </div>
</div>


<?php
// Import search bar module for user to be able to search from homepage.
require_once("search_module.php");

// Generate hottest auctions right now

// find items with highest number of bids. Get itemName and current price to display.
$hot_items_query = "select a.* from 
                      (SELECT a.listingID, ItemName,itemImage, ifnull(max(bidPrice),startPrice) as currentPrice, count(bidID) as num_bids 
                      from auction_listing a 
                      left join bids b on a.listingID = b.listingID 
                      where endTime > now() 
                      group by a.listingID, a.ItemName, a.itemImage) a
                      order by num_bids DESC LIMIT 8";
$hot_items = SQLQuery($hot_items_query);




// Generate reccommended items if logged in, or nothing if not logged in.

?>

<?= console_log($hot_items); ?>




<div class="container">
  <div class="row">
    <div class="col-12">
      <h3 class="text-left">
        Hottest auctions right now
      </h3>
      <div class="row flex-row flex-nowrap p-5" style="height: 330px; overflow-x:auto;">

        <?php
        // print hottest items
        if ($hot_items == false) { // SQL query error, or no items returned at all.
          echo "Sorry, we can't seem to find any auctions. This might be an issue on our side.";
        } else {
          $counter = 0;
          foreach ($hot_items as $row) {
            if ($counter > 8) {
              break;
            } elseif ($row) {
              print_homepage_item_list($row["listingID"], $row["ItemName"], $row["currentPrice"], $row["itemImage"]);
            } else {
              echo "No more listings to show :(";
            }
            ++$counter;
          }
        }

        ?>


      </div>
    </div>
  </div>

  <hr class="my-2">
  <div style="align-items: center;">
    <img src="https://tpc.googlesyndication.com/simgad/13881930544975202200" width="970" height="90" alt="" style="display:block; margin-left:auto; margin-right: auto;">
  </div>
  <hr class="my-2">

  <div class="row">
    <div class="col-md-12">
      <div class="jumbotron">
        <h2>
          Hello, AuctionXpresser!
        </h2>
        <p>
          We have great deals all the time! Why not join in the fun and sell something too!
        </p>
        <p>
          <a class="btn btn-primary btn-large" href="create_auction.php">Create an Auction</a>
        </p>
      </div>
    </div>
  </div>


  <div class="row">
    <div class="col-12">
      <h3 class="text-left">
        Recommendations just for you.
      </h3>
      <div class="row flex-row flex-nowrap p-5" style="height: 330px; overflow-x:auto;">

        <?php
        
        // remember to import recommendations module at the top.

        // show recommended items based on bidding history
          // if user is logged in 
          if ($_SESSION["logged_in"] === true){
              // initialize an array which contains the return value from recommendations
            $recommend_array = getRecommendations();
              // write sql query 
            $recommend_query = "SELECT * from v_aution_info where listingID in (" . implode(',', $recommend_array) . ")";
              // retrieve result
            $recommend_result = SQLQuery($rec_query);

              // if no recommended items returned for user 
            if ($rec_result == false) {
              echo "We can't seem to find your reccomendations right now. Make some bids first and we'll show you some recommended items ";
            } 
            else { // if recommendaations exist, 
                // initialise counter to keep track of how many items are shown
              $counter = 0;
                // loop through recommendations
              foreach ($recommend_result as $row) {
                if ($counter > 8) { // only show 8 recommendations 
                  break;
                } 
                elseif ($row) { // otherwise, if more recommendations exist : 
                  // show the recommended item along with relevant information 
                print_homepage_item_list($row["listingID"], $row["ItemName"], $row["currentPrice"], $row["itemImage"]);
                } 
                else { // if there are no more recommended items to show 
                  echo "No more recommendations to show :(";
                }
                // increment counter 
                ++$counter;
              }
            }
          } 
          else { // if user is not logged in, prompt them to do so.
            echo "You need to be logged in to view your recommendations.";
          }
        ?>
      </div>
    </div>
  </div>
</div>