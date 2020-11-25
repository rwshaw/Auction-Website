<?php include_once("header.php") ?>
<?php
require_once("utilities.php");
require_once("../mysql_connect.php");
require_once("debug.php");
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
?>


<?php
//session delete once implemented

//$_SESSION['logged_in'] = true;
//$_SESSION['user_id'] = 2;
// $_SESSION['account_type'] = "buyer";


// Get info from the URL using a prepared statement to avoid sql injection:

$item_id = $_GET['item_id'];
$con = OpenDbConnection();
$stmt = $con->prepare("SELECT * FROM auction_listing WHERE listingID=?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$get_mysql = $stmt->get_result();
$result = $get_mysql->fetch_assoc();
$stmt->close();
CloseDbConnection($con);

$end_time = new DateTime($result['endTime']);
$now = new DateTime();
$endtimestamp=date_timestamp_get($end_time);
$seller_id = $result['sellerUserID'];
$item_id = $result['listingID'];
$title = $result['itemName'];
$description = $result['itemDescription'];
$imageurl = $result['itemImage'];
$startprice = $result['startPrice'];
//will always return the highest bid during and after auction
$maxprice = "SELECT bidPrice as currentPrice, userID FROM bids WHERE (listingID=$item_id) order by bidID desc";
$highest_bid = SQLQuery($maxprice);
//error_log( print_r($highest_bid[0]['currentPrice'], TRUE) );
$current_price = $highest_bid[0]['currentPrice'];
$highest_bidder = $highest_bid[0]['userID'];
error_log($current_price);
// test if the user has bid on and item returns 1 for bid and 0 for no bid 
if (array_key_exists('logged_in', $_SESSION) && $_SESSION['logged_in'] == true) {
  $userID = $_SESSION['user_id'];
  $query = "SELECT count(1) FROM bids WHERE (userID='$userID' AND listingID='$item_id')";
  $bidstatus = SQLQuery($query);
  error_log($bidstatus[0]['count(1)']);
}

//error_log(print_r($current_price, TRUE));
$num_bids = 1;



// TODO: Note: Auctions that have ended may pull a different set of data,
//       like whether the auction ended in a sale or was cancelled due
//       to lack of high-enough bids. Or maybe not.

// Calculate time to auction end:
$now = new DateTime();

if ($now < $end_time) {
  $time_to_end = date_diff($now, $end_time);
  $time_remaining = ' (in ' . display_time_remaining($time_to_end) . ')';
}

// TODO: If the user has a session, use it to make a query to the database
//       
//
$has_session = false;
if (array_key_exists('logged_in', $_SESSION) && $_SESSION['logged_in'] == true) {
  $has_session = true;
  $has_session = isset($_SESSION);
  $query = "SELECT isWatching FROM watchlist WHERE (userID='$userID' AND listingID='$item_id')";
  $watching = SQLQuery($query);
  $watching = $watching[0]['isWatching'];
  
}
?>

<div class="container">
  <div class="p-3">
    <div class="row">
      <!-- Row #1 with auction title + watch button -->
      <div class="col-sm-8">
        <!-- Left col -->
        <h2 class="my-3"><?php echo ($title); ?></h2>
      </div>
      <div class="col-sm-4 align-self-center">
        <!-- Right col -->
        <?php
        /* The following watchlist functionality uses JavaScript, but could
     just as easily use PHP as in other places in the code */
        if (($now < $end_time) && (isset($_SESSION['user_id']))) :
        ?>
          <div id="watch_nowatch" <?php if ($has_session && $watching==true) echo ('style="display: none"'); ?>>
            <button id="watchclick" type="button" class="btn btn-outline-secondary btn-sm" onclick="addToWatchlist()">+ Add to watchlist</button>
          </div>
          <div id="watch_watching" <?php if (!$has_session || !$watching) echo ('style="display: none"'); ?>>
            <button type="button" class="btn btn-success btn-sm" disabled>Watching</button>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeFromWatchlist()">Remove watch</button>
          </div>
        <?php endif /* Print nothing otherwise */ ?>
      </div>
    </div>

    <div class="row">
      <!-- Row #2 with image -->
      <div class="col-sm-8">
        <!-- Left col with item info -->


        <img src=<?php echo ($imageurl) ?> class="img-fluid" alt="Responsive Image" width="307" height="240" />
      </div>

    </div>
  </div>
  <div class="row">
    <!-- Row #3 with auction description + bidding info -->
    <div class="col-sm-8">
      <!-- Left col with item info -->

      <div class="itemDescription">
        <?php echo ($description); ?>
      </div>

    </div>


    <div class="col-sm-4">
      <!-- Right col with bidding info -->

      <p>
        <?php if ($now > $end_time) : ?>
          This auction ended <?php echo (date_format($end_time, 'j M H:i')); ?>
          <div class="row">
            Bidding has ended, the winning bid was: £<?php echo ($current_price); ?>
          </div>
          <?php if (isset($bidstatus)) : ?>
            <?php if ($userID == $highest_bidder) : ?>
              <div class="row">
                You won this auction!
              </div>
            <?php else : ?>
              You were outbid!
            <?php endif ?>
          <?php endif ?>
        <?php else : ?>
          Auction ends <?php echo (date_format($end_time, 'j M H:i') . $time_remaining) ?>
        </p>
          <p class="lead" id="latestbid"></p>
          <?=console_log("I'm in last ELSE");?>
      <!-- Show bidding Bidding form if logged out or not the seller otherwise hide -->
          
    <?php if (!isset($_SESSION['user_id']) || ($_SESSION['user_id']!=$seller_id)) : ?>
      <form method="POST" action="place_bid.php">
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text">£</span>
          </div>
          <input type="number" class="form-control" id="bid" data-toggle="popover" data-placement="left" data-content="Please enter a bid higher than the current bid">
        </div>
        <button type="button" class="btn btn-primary form-control" onclick="placeBid()">Place bid</button>
      </form>
      <?php endif ?>
    <?php endif ?>


    </div> <!-- End of right col with bidding info -->
  </div>
</div> <!-- End of row #2 -->

</div> <!-- End of row #2 -->

<?=console_log($maxprice);?>


<?php include_once("footer.php") ?>

<script>
  // Make Ajax call to get latest bids
  // on success empty table, repopulate with 10 latets bids. Add extra html /css to show your bid, winning bid

  $(document).ready(function() {
    bidUpdater();
    setInterval(bidUpdater, 3000);
  });


  function bidUpdater() {
    $.ajax("priceupdate.php", {
      type: "POST",
      data: {
        functionname: "updateprice",
        arguments: [<?php echo ($item_id); ?>,<?php echo ($startprice); ?>]
      },

      success: function(htmlResult) {

        console.log(htmlResult);
        $("#latestbid").html("Current Price: £".concat(htmlResult));
        // setTimeout(bidUpdater(), 3000);
        // if all looks ok, update the DOM to show latest data feed. jQuery
        // $(#table). empty
        // $(#table). repopualte with new html order by bidID desc
      },
      error: function(obj, textstatus) {
        console.log("Error");
      }
    });
  }


  function placeBid(button) {
    var bid = document.getElementById("bid").value;
    console.log(bid);
    // This performs an asynchronous call to a PHP function using POST method.
    // Sends item ID as an argument to that function.

    $.ajax('place_bid.php', {
      type: "POST",
      dataType: "text",
      data: {
        functionname: 'place_bid',
        arguments: [<?php echo ($item_id); ?>, bid, <?php echo ($startprice); ?>, <?php echo ($endtimestamp); ?>]

      },

      success: function(text) {
        // Callback function for when call is successful and returns obj
        console.log("RAN SUCCESS");

        var status = text.trim();
        console.log(status);
        console.log(typeof(status));
        console.log(typeof("bidplaced"));
        if (status === "bidplaced") {
          console.log("Yay");
          $("#watchclick").click();
          bidUpdater();
          bidNotification(); // on bidplaced success, run notification generation for all users on item watchlist
        } else if (status == "login") {
          // if bid is too small popover warning to bid
          console.log("nay");
          $("#loginpopup").click();
          //$('#bid').popover('show');
          //setTimeout(function() {$('#bid').popover('dispose');}, 5000);
        } else {
          $('#bid').popover('show');
          setTimeout(function() {
            $('#bid').popover('dispose');
          }, 5000);
        }
      }
    });
  }

  // JavaScript functions: addToWatchlist and removeFromWatchlist.

  // watchlist add success send email func

  function add_to_watch_success() {
    $.ajax("watchlist_notifications.php", {
      type: "POST",
      data: {
        functionname: 'add_watch_email',
        arguments: [<?php echo ($item_id); ?>],
        username: [<?php echo ((isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : null ); ?>]
      },

      success: function(obj, textstatus) {
        console.log("Email sent");
        var resObj = obj.trim();
        console.log(resObj);
      },
      error: function(obj, textstatus) {
        console.log("Error");
      }
    });
  }

  function addToWatchlist(button) {

    // This performs an asynchronous call to a PHP function using POST method.
    // Sends item ID as an argument to that function.
    $.ajax('watchlist_funcs.php', {
      type: "POST",
      data: {
        functionname: 'add_to_watchlist',
        arguments: [<?php echo ($item_id); ?>],
        username: [<?php echo ((isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : null ); ?>]
      },

      success: function(obj, textstatus) {
        // Callback function for when call is successful and returns obj
        console.log("Success");
        var objT = obj.trim();
        console.log(objT);

        if (objT == "success") {
          $("#watch_nowatch").hide();
          $("#watch_watching").show();
          // TODO call function to send email.
          add_to_watch_success();
        } else {
          var mydiv = document.getElementById("watch_nowatch");
          mydiv.appendChild(document.createElement("br"));
          mydiv.appendChild(document.createTextNode("Add to watch failed. Try again later."));
        }
      },

      error: function(obj, textstatus) {
        console.log("Error");
      }
    }); // End of AJAX call

  } // End of addToWatchlist func

  // watchlist removal success send email func
  function remove_from_watch_success() {
    $.ajax("watchlist_notifications.php", {
      type: "POST",
      data: {
        functionname: 'remove_watch_email',
        arguments: [<?php echo ($item_id); ?>],
        username: [<?php echo ((isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : null ); ?>]
      },

      success: function(obj, textstatus) {
        console.log("Email sent");
        var resObj = obj.trim();
        console.log(resObj);
      },
      error: function(obj, textstatus) {
        console.log("Error");
      }
    });
  }

  function removeFromWatchlist(button) {
    // This performs an asynchronous call to a PHP function using POST method.
    // Sends item ID as an argument to that function.
    $.ajax('watchlist_funcs.php', {
      type: "POST",
      data: {
        functionname: 'remove_from_watchlist',
        arguments: [<?php echo ($item_id); ?>],
        username: [<?php echo ((isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : null ); ?>]
      },

      success: function(obj, textstatus) {
        // Callback function for when call is successful and returns obj
        console.log("Success");
        var objT = obj.trim();

        if (objT == "success") {
          $("#watch_watching").hide();
          $("#watch_nowatch").show();
          // TODO call function to send email
          remove_from_watch_success();
        } else {
          var mydiv = document.getElementById("watch_watching");
          mydiv.appendChild(document.createElement("br"));
          mydiv.appendChild(document.createTextNode("Watch removal failed. Try again later."));
        }
      },

      error: function(obj, textstatus) {
        console.log("Error");
      }
    }); // End of AJAX call

  } // End of addToWatchlist func

  // Successful place bid results in notification generation for watchlist users.
  // function to send item_id to watchlist_notifications.php to initiate notifications for latest bid.
  function bidNotification() { // Should be implemented on success on place bid ajax call.
    $.ajax("watchlist_notifications.php", {
      type: "POST",
      data: {
        functionname: 'bid_notification',
        arguments: [<?php echo ($item_id); ?>],
        username: [<?php echo ((isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : null ); ?>]
      },

      success: function(obj, textstatus) {
        // console.log("function returned success");
        var resObj = obj.trim();
        // console.log(resObj);
      },
      error: function(obj, textstatus) {
        console.log("Error");
      }
    });
  }
</script>