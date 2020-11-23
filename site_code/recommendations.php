<?php include_once("header.php")?>
<?php require("utilities.php")?>

<div class="container">

<h2 class="my-3">Recommendations for you</h2>

<?php

  error_reporting(E_ALL);
  ini_set('display_errors', '1');

  // import sql connection script
  require_once('mysql_connect.php'); 

  // open connection to database
  $db = OpenDbConnection(); 

  // Check if the user is already logged in, if yes then redirect him to welcome page
  if(!isset($_SESSION["logged_in"]) && !$_SESSION["logged_in"] === true) {
    header("location: browse.php"); 
    exit; 
  }

    // get user id of logged-in user 
  $user_id = '1';
  echo "user_id: " . $user_id . "<br><br>";
  // $user_id = $_SESSION['user_id']; 
  // echo $_SESSION['user_id']; 

  // initialie matrix and user array 
  $matrix = array();
  $user_array = array();
  $listing_array = array();

  // retrieve all users
    // prepare query 
  $all_users_query = $db->prepare("SELECT userID FROM users"); 
    // execute prepared statement 
  $all_users_query->execute();
    // retrieve result
  $all_users = $all_users_query->get_result();    
    // fetch all results
  while ($users = $all_users->fetch_array(MYSQLI_ASSOC)) {
      // loop through results 
    foreach ($users as $user) {
        // add users to user_array 
      $user_array[] = $user;
    } 
  }

  // retrieve all listings 
    // prepare query
  $all_listings_query = $db->prepare("SELECT listingID FROM auction_listing");
    // execute result
  $all_listings_query->execute();
    // retrieve results 
  $all_listings = $all_listings_query->get_result(); 
    // fetch all listings 
  while ($listings = $all_listings->fetch_array(MYSQLI_ASSOC)) {
      // loop over all listings  
    foreach ($listings as $listing) {
        // add users to listing_array
      $listing_array[] = $listing;
    }
  }
  
  // fill matrix with bid preferences of users who bidded on listings 
    // loop over all users
  foreach ($user_array as $user_value) {
      // loop over all listings 
    foreach ($listing_array as $listing_value) {

      // retrieve bid preference of user  
        // prepare query
      $check_user_bid_query = $db->prepare("SELECT DISTINCT listingID FROM bids WHERE userID=? AND listingID=?");
        // bind parameters
      $check_user_bid_query->bind_param("ss", $user_value, $listing_value);
        // execute result
      $check_user_bid_query->execute();
        // retrieve results
      $check_user_bid = $check_user_bid_query->get_result();
        // fetch array
      $user_bid = $check_user_bid->fetch_assoc();

      // check whether bid preference was returned or note (i.e. if user made a bid on this listing or not)
        // if user didn't bid on this particular listing, insert 0 into matrix 
      if (is_null($user_bid)) {
        $matrix[$user_value][$listing_value] = "0";
      } 
      else { // else if user did bid, insert 1
        $matrix[$user_value][$listing_value] = "1";
      }
    }
  }

  // FOR TESTING
  echo "<br>"; echo '<pre>'; print_r($matrix); echo '</pre>'; 



?>