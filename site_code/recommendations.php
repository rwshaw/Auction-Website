<?php include_once("header.php")?>
<?php require("utilities.php")?>

<div class="container">

<h2 class="my-3">Recommendations for you</h2>

<?php

  error_reporting(E_ALL);
  ini_set('display_errors', '1');

  // import sql connection script
  require_once('../mysql_connect.php'); 

  // open connection to database
  $db = OpenDbConnection(); 

  // Retrieve all bids the user has made 
    // get user id of logged-in user 
  $user_id = '1';
  echo $user_id;
  // $user_id = $_SESSION['user_id']; 
  // echo $_SESSION['user_id']; 

    // prepare sql statement
  $all_user_bids_query = $db->prepare("SELECT bidID FROM bids where userID=?");
    // bind parameters
  $all_user_bids_query->bind_param("s",$user_id); 
    // execute prepared statement 
  $all_user_bids_query->execute();
    // retrieve result
  $result = $all_user_bids_query->get_result(); 
    // fetch as associative array 
  // $user_bids = $result->fetch_assoc();

  while ($user_bids = $result->fetch_assoc()) {
    echo '<pre>'; print_r($user_bids); echo '</pre>';
  }

  

  // $row = $result->fetch_array(MYSQLI_NUM);


  // $check_user_query->bind_param("s",$email);   
  //   // execute prepared statement 
  // $check_user_query->execute(); 
  //   // retrieve result
  // $result = $check_user_query->get_result(); 
  //   // fetch as associative array
  // $user = $result->fetch_assoc();
  //   // check if user exists with this email address
  // if ($user && $user['email'] == $email) { // if user does exist, redirect back to registration

?>

<?php  

  // This page is for showing a buyer recommended items based on their bid 
  // history. It will be pretty similar to browse.php, except there is no 
  // search bar. This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.
  
  
  // TODO: Check user's credentials (cookie/session).
  
  // TODO: Perform a query to pull up auctions they might be interested in.
  
  // TODO: Loop through results and print them out as list items.



  // MY NOTES

  // Definition: In the context of recommendation systems, collaborative filtering is a method of making predictions about the interests of user by analysing the taste of users which are similar to the said user. The idea of filtering patterns by collaborating multiple viewpoints is why it is called collaborative filtering.
        // https://www.youtube.com/watch?v=6mGMBipt7kU

	// Brief: Buyers can receive recommendations for items to bid on based on collaborative filtering (i.e., ‘you might want to bid on the sorts of things other people, who have also bid on the sorts of things you have previously bid on, are currently bidding on).
    // so find users with similar bidding histories to the current user, see what they are bidding on right now, reccomend those items to the user. (i.e. based on preference of similar bidders)

  // 3 types of reccomendation: 
    // user based
      // historical preferences in terms of views, watchlists, etc. 
        // assumes historical preferences are a good signal for future preferences 
        // measured by explicit ratings (e.g. likes) or implicit (views, clicks, purchase records)
    // Nearest neighbourhood algorithm using Person correlation or cosine similarity       
    // item based
      // 
    // content based 
      // can use product categories

	// at bottom of homepage, reccomend items based on: 
    // 1) most popular items - the ones that are currently being bid on by other users with similar bidding histories to the currrent user. 
    // 2) most popular items by viewing traffic; i.e. items which have received a lot of views by all users across the site
    // 3) similarity analysis based on the current user's viewing history and other user's viewing history

	// at bottom of auction page, reccomend items based on: 
	   // 1) items in the same category 
     // 2) watchlist-ability - items watchlisted by other users who have also watchlisted this item


  // get list of bidders who bid for the same items as the user 
    // SELECT items user bid on 
    // SELECT bidders who bid on that item (excluding the user)
  // find all items those bidders bid on. 
    // SELECT all bids 
  // find items those bidders have bid on which are still live and the current user hasnt bid on 
    // order by the highest number of bids 
  // display the top 10 item not bidded on by the user 


  

  
?>
