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
          <img class="d-block w-100" style="margin: 0; position: absolute; top: 50%; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%);"   src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1589&q=80" />
        </div>
        <div class="carousel-caption">
            <h4>
              Welcome to AUCTIONXPRESS.
            </h4>
          </div>
        <div class="carousel-item active" style="height: 350px;">
          <img class="d-block w-100" style="margin: 0; position: absolute; top: 50%; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%);"   alt="Carousel Bootstrap Second" src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1500&q=80" />
        </div>
        <div class="carousel-caption">
            <h4>
              Welcome to AUCTIONXPRESS.
            </h4>
          </div>
        <div class="carousel-item" style="height: 350px;">
          <img class="d-block w-100" style="margin: 0; position: absolute; top: 50%; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%);"   alt="Carousel Bootstrap Third" src="https://images.unsplash.com/photo-1497515098781-e965764ab601?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1608&q=80" />
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

<div class="container">

  <h3 class="my-3">Looking for something? Try searching.</h3>

  <div id="searchSpecs">
    <!-- When this form is submitted, this PHP page is what processes it.
     Search/sort specs are passed to this page through parameters in the URL
     (GET method of passing data to a page). -->
    <form method="get" action="browse.php">
      <div class="row">
        <div class="col-md-5 pr-0">
          <div class="form-group">
            <label for="keyword" class="sr-only">Search keyword:</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text bg-transparent pr-0 text-muted">
                  <i class="fa fa-search"></i>
                </span>
              </div>
              <input type="search" class="form-control border-left-0" id="keyword" name="keyword" placeholder="Search for a product">
            </div>
          </div>
        </div>
        <div class="col-md-3 pr-0">
          <div class="form-group">
            <label for="cat" class="sr-only">Search within:</label>
            <select class="form-control" id="cat" name="cat">
              <option value=''>All categories</option>
              <!-- TODO - Auto generate categories alphabetically in options from database -->
              <?php
              $sql = "SELECT distinct deptName from auctionsite.categories order by deptName";
              $result = SQLQuery($sql);
              foreach ($result as $row) {
                echo "<option value=" . $row["deptName"] . ">" . $row["deptName"] . "</option>";
              }
              ?>
            </select>
          </div>
        </div>
        <div class="col-md-3 pr-0">
          <div class="form-inline">
            <label class="mx-2" for="order_by">Sort by:</label>
            <select class="form-control" id="order_by" name="order_by">
              <option value="date">Soonest expiry</option>
              <option value="pricelow">Price (low to high)</option>
              <option value="pricehigh">Price (high to low)</option>
            </select>
          </div>
        </div>
        <div class="col-md-1 px-0">
          <button type="submit" class="btn btn-primary">Search</button>
        </div>
      </div>
    </form>
  </div>
  <!-- JS to retain form data in form options after input. -->
  <script type="text/javascript">
    document.getElementById("keyword").value = "<?php echo $_GET["keyword"]; ?>";
    document.getElementById("cat").value = "<?php echo $_GET["cat"]; ?>";
    document.getElementById("order_by").value = "<?php echo $_GET["order_by"]; ?>";
  </script>
  <!-- end search specs bar -->

  <?php
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




  // Generate reccommended items if logged in, or other items of interest if not logged in.

  ?>

  <?= console_log($hot_items);?>


</div>

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
        }
        elseif ($row) {
          print_homepage_item_list($row["listingID"],$row["ItemName"], $row["currentPrice"],$row["itemImage"]);
        }
        else {
          echo "No more listings to show :(";
        }
        ++$counter;
      }
      }
      
      ?>


      </div>
    </div>
  </div>

  <hr class="my-4">


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
      // print recommended items
      echo "We are still working on recommendations for you. Keep shopping!"
      
      ?>


      </div>
    </div>
  </div>
</div>