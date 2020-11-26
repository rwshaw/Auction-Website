<?php require_once("../mysql_connect.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); ?>

<?php

// display_time_remaining:
// Helper function to help figure out what time to display
function display_time_remaining($interval)
{

  if ($interval->days == 0 && $interval->h == 0) {
    // Less than one hour remaining: print mins + seconds:
    $time_remaining = $interval->format('%im %Ss');
  } else if ($interval->days == 0) {
    // Less than one day remaining: print hrs + mins:
    $time_remaining = $interval->format('%hh %im');
  } else {
    // At least one day remaining: print days + hrs:
    $time_remaining = $interval->format('%ad %hh');
  }

  return $time_remaining;
}

// print_listing_li:
// This function prints an HTML <li> element containing an auction listing
function print_listing_li($item_id, $title, $desc, $price, $num_bids, $end_time, $category, $subcategory)
{
  // Truncate long descriptions
  if (strlen($desc) > 250) {
    $desc_shortened = substr($desc, 0, 250) . '...';
  } else {
    $desc_shortened = $desc;
  }

  // Fix language of bid vs. bids
  if ($num_bids == 1) {
    $bid = ' bid';
  } else {
    $bid = ' bids';
  }

  // Calculate time to auction end

  $end_time = DateTime::createFromFormat('Y-m-d H:i:s', $end_time); //reformat date from string to datetime.
  $now = new DateTime();
  if ($now > $end_time) {
    $time_remaining = 'This auction has ended';
  } else {
    // Get interval:
    $time_to_end = date_diff($now, $end_time);
    $time_remaining = display_time_remaining($time_to_end) . ' remaining';
  }

  // Print HTML
  echo ('
    <li class="list-group-item d-flex justify-content-between">
    <div class="p-2 mr-5">
    <h5><a href="listing.php?item_id=' . $item_id . '" aria-labelledby="catTree">' . $title . '</a>
    <small id=catTree class="text-muted font-italic">
    ' . $category . ' -> ' . $subcategory . '
  </small>
    </h5>
   ' . $desc_shortened . '</div>
    <div class="text-center text-nowrap"><span style="font-size: 1.5em">£' . number_format($price, 2) . '</span><br/>' . $num_bids . $bid . '<br/>' . $time_remaining . '</div>
  </li>');
}

//HTML BOOTSTRAP Alert Generator.
function  print_alert($alert_type, $alert_heading, $alert_text1, $alert_text2)
{
  /**
   * Creates an alert message.
   * 
   * @param string $alert_type - colour of alert (success/danger/warning/info etc)
   * @param string $alert_heading - The heading (already put between h4 tags)
   * @param string $alert_text1 - Above margin text (already put between p tags)
   * @param string $alert_text2 - Below margin text (already put between p tags)
   * @return string html alert
   */
  echo
    '<div class="alert alert-' . $alert_type . '" alert-dismissable fade show role="alert">
    <h4 class="alert-heading">' . $alert_heading . '</h4>
    <p>' . $alert_text1 . '    
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
    </button></p>
    <hr>
    <p class="mb-0">' . $alert_text2 . '</p>
  </div>';
}

function send_user_email($user_id, $subject, $message)
{ // email currently not working for Q on mac...
  $headers = array();
  $headers['MIME-Version'] = '1.0';
  $headers['Content-type'] = 'text/html; charset=iso-8859-1';
  $headers['From'] = 'team@auctionxpress.com';

  //get user email from user_id
  $user_email_query = "SELECT email FROM users WHERE userID = $user_id";
  $result = SQLQuery($user_email_query);
  $user_email = $result[0]["email"];
  $message = wordwrap($message, 70, "\r\n"); // set character wrap in case lines > 70

  $success = mail($user_email, $subject, $message, $headers);
  if (!$success) {
    $errmsg = error_get_last()['message'];
  }
  return $success;
}

function print_watchlist_listing($item_id, $title, $desc, $price, $num_bids, $end_time, $category, $subcategory)
{
  // Truncate long descriptions
  if (strlen($desc) > 250) {
    $desc_shortened = substr($desc, 0, 250) . '...';
  } else {
    $desc_shortened = $desc;
  }

  // Fix language of bid vs. bids
  if ($num_bids == 1) {
    $bid = ' bid';
  } else {
    $bid = ' bids';
  }

  // Calculate time to auction end

  $end_time = DateTime::createFromFormat('Y-m-d H:i:s', $end_time); //reformat date from string to datetime.
  $now = new DateTime();
  if ($now > $end_time) {
    $time_remaining = 'This auction has ended';
  } else {
    // Get interval:
    $time_to_end = date_diff($now, $end_time);
    $time_remaining = display_time_remaining($time_to_end) . ' remaining';
  }

  // Print HTML
  echo ('<div class="list-group-item ">
  <div class="row justify-content-start align-items-start p-1">
      <div class="col-7"><h5><a href="listing.php?item_id=' . $item_id . '" aria-labelledby="catTree">' . $title . '</a>
      <small id=catTree class="text-muted font-italic">
      ' . $category . ' -> ' . $subcategory . '
    </small>
      </h5><span class="text-dark"> ' . $desc_shortened . '<br>' . $time_remaining . '</span>
      </div>
      <div class="col-5" >
      <table class="table table-dark table-striped table-hover table-sm table-borderless" id="' . $item_id . '">
      <tr class="bg-success"><td>WINNING BID</td></tr>
      <tr><td>2nd</td></tr>
      <tr><td>3rd</td></tr>
      <tr><td>4th</td></tr>
      <tr><td>5th</td></tr>
      </table>
      </div>
  </div>
</div>');
}

function print_homepage_item_list($item_id, $itemName, $price, $itemImage)
{

  // Truncate long itemnames
  if (strlen($itemName) > 20) {
    $itemName = substr($itemName, 0, 18) . '...';
  } else {
    $itemName = $itemName;
  }

  echo ('<div class="col-3" >
  <div class="image-wrapper">
  <div class="image-wrapper2">
          <a href="listing.php?item_id=' . $item_id . '">
          <img style="height: 90%; max-width:100%;
          max-height: 100%; width: auto;" src="' . ((is_null($itemImage) or ($itemImage == "")) ? "data:image/png;base64,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" : $itemImage) . '" class="rounded item-image" />
          </div> </div>
          <div style="display: flex; height: 10%; width: 80%;
      justify-content: space-between;">
            <p style="color: black;">' . $itemName . '</p>
            <p style="color: black;">£' . number_format($price, 2) . '</p>
          </div>
          </a>
        </div>');
}
