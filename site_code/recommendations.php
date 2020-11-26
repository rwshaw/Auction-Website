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
      $user_bid = $check_user_bid->fetch_array(MYSQLI_ASSOC);

      // check whether bid preference was returned or not (i.e. if user made a bid on this listing or not)
        // if user didn't bid on this particular listing, insert 0 into matrix 
      if (is_null($user_bid)) {
        $matrix[$user_value][$listing_value] = "0";
      } 
      else { // else if user did bid, insert 1
        $matrix[$user_value][$listing_value] = "1";
      }
    }
  }


  // store logged_in_user's bid for similarity analysis later
    // initialise emtpy array to store user bids 
  $logged_in_user_bids = array();
    // if we find logged_in_user in matrix, 
  if (array_key_exists($user_id, $matrix)) {
      // store their bid preferences in logged_in_user_bids
    $logged_in_user_bids[] = $matrix[$user_id];
  }
    // make sure bid preferences are first thing to come up 
  $user_bid_preferences = $logged_in_user_bids[0];
  // FOR TESTING
  // print_r($user_bid_preferences);


  // stored other user's bid preferences and conduct similarity analysis 
    // 
  $similarity_score = array();
    // extract users from matrix 
  $matrix_user_ids = array_keys($matrix);
                // FOR TESTING
                // print_r($matrix_user_ids); echo "<br>";
    // loop through users
  foreach ($matrix_user_ids as $user_ids) {
      // initialie empty array to store other user's bids before checking a user 
    $other_user_bids = array();
      // check if user is logged in user 
    if ($user_ids != $user_id) {
        // if not logged in user, store the current user's preferences in other_user_bids
      $other_user_bids[] = $matrix[$user_ids];
        // make sure bid preferences are first thing to come up 
      $other_users_bid_preferences = $other_user_bids[0];
        // get similarity score for user 
      $result = jaccardSimilarlity($user_bid_preferences, $other_users_bid_preferences);
      // echo "user: $user_ids ";echo $result; echo "<br>";
        // store all similarity scores in the score array in same order as matrix 
      $similarity_score[$user_ids] = $result; 
    }
  }


  // prediction of rating user u will give item i = 

  // intiailise an empty array: $total and empty array: $similarity_sums
  

// find the most similar user in terms of bid history, reccomend the items theyve bid on that the user has not bid on yet 

  // identify the most similar users 
    // loop through similarity_score array 
    // return top N users with highest similarity score (where N = 5)
  
  // identify the items the similar user has bid on that the user has not 
    // loop through the items for that user 
      // if the similar user has bid on those items and the user has not bid on those items 
    // reccomend those items 
      // return an array with those items. 


  
  // FOR TESTING
  echo "<pre>", print_r($similarity_score), "</pre>";
  echo "<br>"; echo '<pre>'; print_r($matrix); echo '</pre>'; 

  // https://helpful.knobs-dials.com/index.php/Similarity_or_distance_measures/metrics
  // function to calculate jaccard similarlity as boolean intersection / boolean union
  function jaccardSimilarlity ($user_array, $other_user_array) {

    // initialise counter variables for intersection and union
    $intersection_count = 0; 
    $union_count = 0;

    // loop through user array and the other user array
    foreach (array_keys($user_array) as $key) {
      // if both users have bid on the item (sum of pairwise multiplication)
      if ($user_array[$key] == $other_user_array[$key] && $user_array[$key] == "1" && $other_user_array[$key] == "1") {
        // increment intersection counter 
        $intersection_count += 1; 
      }
      // if at lease one user has bid on the item (sum of pairwise addition)
      if ($user_array[$key] + $other_user_array[$key] >= 1) {
        // increment union counter
        $union_count += 1;
      }
    }

    // caclulate jaccard similarlity value 
    $jaccard_similarity = $intersection_count / $union_count;
    
    // return jaccard similarlity value 
    return $jaccard_similarity;

    // notes
    // intersection calculation = sum(RHS) = 0 + 0 + 0 + 1 = 1
      // 0 x 0 = 0
      // 0 x 1 = 0
      // 1 x 0 = 0
      // 1 x 1 = 1

    // unio calculation = sum (RHS) = 0 + 1 + 1 + 1 = 4
      // 0 + 0 = 0 
      // 0 + 1 = 1
      // 1 + 0 = 1
      // 1 + 1 = 1
  }

?>
