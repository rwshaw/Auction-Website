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
