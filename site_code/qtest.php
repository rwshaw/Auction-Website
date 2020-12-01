<?php include_once("header.php") ?>
<?php
require_once("utilities.php");
require_once("../mysql_connect.php");
require_once("debug.php");
?>

<div>
    <h1>
        THIS IS Q TEST PAGE
    </h1>
</div>


<?php

$item_id = 91;
$userid = 4; // should be session user id

$check_query = "SELECT isWatching FROM watchlist where userID = $userid and listingID=$item_id";
$result = SQLQuery($check_query);
if (is_null($result)) {
    $current_value = FALSE;
} else {
    $current_value = $result[0]["isWatching"];
}
$watching = $current_value;

?>

<?= console_log($watching); ?>

<div id="watch_nowatch" <?php if ($watching) echo ('style="display: none"'); ?>>
    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addToWatchlist()">+ Add to watchlist</button>
</div>
<div id="watch_watching" <?php if (!$watching) echo ('style="display: none"'); ?>>
    <button type="button" class="btn btn-success btn-sm" disabled>Watching</button>
    <button type="button" class="btn btn-danger btn-sm" onclick="removeFromWatchlist()">Remove watch</button>
</div>
<div class="m-5">
    <button type="button" class="btn btn-primary" onclick="bidNotification()"> Add notifications for last bid</button>
</div>


<script> 
// JavaScript functions: addToWatchlist and removeFromWatchlist.

// watchlist add success send email func

function add_to_watch_success() {
            $.ajax("watchlist_notifications.php", {
              type: "POST",
              data: {functionname: 'add_watch_email', arguments: [<?php echo($item_id);?>], 
            username: [<?php echo($_SESSION['user_id']);?>]},

              success: 
                function(obj, textstatus) {
                  console.log("Email sent");
                  var resObj = obj.trim();
                  console.log(resObj);
                },
              error: 
                function (obj, textstatus) {
                console.log("Error");
              }
            }
            );
        }

function addToWatchlist(button) {
  console.log("These print statements are helpful for debugging btw");

  // This performs an asynchronous call to a PHP function using POST method.
  // Sends item ID as an argument to that function.
  $.ajax('watchlist_funcs.php', {
    type: "POST",
    data: {functionname: 'add_to_watchlist', arguments: [<?php echo($item_id);?>], 
            username: [<?php echo($_SESSION['user_id']);?>]},

    success: 
      function (obj, textstatus) {
        // Callback function for when call is successful and returns obj
        console.log("Success");
        var objT = obj.trim();
 
        if (objT == "success") {
          $("#watch_nowatch").hide();
          $("#watch_watching").show();
          // TODO call function to send email.
          add_to_watch_success();
        }
        else {
          var mydiv = document.getElementById("watch_nowatch");
          mydiv.appendChild(document.createElement("br"));
          mydiv.appendChild(document.createTextNode("Add to watch failed. Try again later."));
        }
      },

    error:
      function (obj, textstatus) {
        console.log("Error");
      }
  }); // End of AJAX call

} // End of addToWatchlist func

// watchlist removal success send email func
function remove_from_watch_success() {
            $.ajax("watchlist_notifications.php", {
              type: "POST",
              data: {functionname: 'remove_watch_email', arguments: [<?php echo($item_id);?>], 
              username: [<?php echo($_SESSION['user_id']);?>]
              },

              success: 
                function(obj, textstatus) {
                  console.log("Email sent");
                  var resObj = obj.trim();
                  console.log(resObj);
                },
              error: 
                function (obj, textstatus) {
                console.log("Error");
              }
            }
            );
        }

function removeFromWatchlist(button) {
  // This performs an asynchronous call to a PHP function using POST method.
  // Sends item ID as an argument to that function.
  $.ajax('watchlist_funcs.php', {
    type: "POST",
    data: {functionname: 'remove_from_watchlist', arguments: [<?php echo($item_id);?>], 
            username: [<?php echo($_SESSION['user_id']);?>]},

    success: 
      function (obj, textstatus) {
        // Callback function for when call is successful and returns obj
        console.log("Success");
        var objT = obj.trim();
 
        if (objT == "success") {
          $("#watch_watching").hide();
          $("#watch_nowatch").show();
          // TODO call function to send email
          remove_from_watch_success();
        }
        else {
          var mydiv = document.getElementById("watch_watching");
          mydiv.appendChild(document.createElement("br"));
          mydiv.appendChild(document.createTextNode("Watch removal failed. Try again later."));
        }
      },

    error:
      function (obj, textstatus) {
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
                username: [<?php echo($_SESSION['user_id']);?>]
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