
<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php include_once("mysql_connect.php")?>
<?php  session_start(); ?>
<?php
//TODO: configure proper session values
$_SESSION['email'] = "tom.cruise@ourauctionsite.com";
$_SESSION['account_type'] = "seller"; 
$email = $_SESSION['email'];
$account_type = $_SESSION['account_type'];
?>
<div class="container">

<h2 class="my-3">My listings</h2>

<?php
// Redirect user if incorrect validation 
//echo $email;
/*
if (!isset($_SESSION['email']) || ($_SESSION['account_type'] != "seller"])) 
{
header("Location:createNewUser.php");	
}
//else
{	
$connect = OpenDbConnection();
}
*/
$connect = OpenDbConnection();

//Queries to gather and display relebant information about current user's listings
$sql1 = "SELECT userID FROM users WHERE email = '$email'";
$result1 = SQLQuery($sql1);


$sellerid = $result1[0][userID];


$sql2 = "SELECT * FROM auction_listing WHERE sellerUserID = '$sellerid'";
$result2 = SQLQuery($sql2);

$sql3 = "SELECT fName, lName FROM users WHERE userID = '$sellerid'";
$sellername = SQLQuery($sql3);
$fullname = ($sellername[0][fName])." ".($sellername[0][lName]);

//TODO: Remove auction listings that have already finished


/* NOT WORKING CURRENTLY
// Calculate time to auction end
$end_time = DateTime::createFromFormat('Y-m-d H:i:s', $end_time);
//reformat date from string to datetime.
$now = new DateTime();
if ($now > $end_time) {
    $time_remaining = 'This auction has ended';
}

else {
   // Get interval:
   $time_to_end = date_diff($now, $end_time);
   $time_remaining = display_time_remaining($time_to_end) . ' remaining';
} */



foreach ($result2 as $row) {
	$lid = ($row[listingID]); 
	$iname = ($row[itemName]);
	$ides = ($row[itemDescription]);
	($row[startPrice]);
	$endt = ($row[endTime]);
	($row[categoryID]);
	//($row[itemPic]);
	//($row[currentBid]);
	//$link = "item_details.php?item_id=";
	//$item_details = $link.$iid;
	echo "<div class='item'>";
	echo "<div class='item_row'>Listing ID: $lid</div>";
	echo "<div class='item_row'>Seller Name: $fullname</div>";
	echo "<div class='item_row'>Item Name: $iname</div>";
	echo "<div class='item_row'>Item Description: $ides</div>";
	echo "<div class='item_row'>Auction Ends: $endt</div>";
	echo "<br>";
}




CloseDbConnection($connect);
  // This page is for showing a user the auction listings they've made.
  // It will be pretty similar to browse.php, except there is no search bar.
  // This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.
  
  
  // TODO: Check user's credentials (cookie/session).
  
  // TODO: Perform a query to pull up their auctions.
  
  // TODO: Loop through results and print them out as list items.
  
?>

<?php include_once("footer.php")?>