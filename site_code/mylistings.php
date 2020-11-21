
<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php include_once("mysql_connect.php")?>
<?php  //session_start(); ?>
<?php



$user_id = $_SESSION['user_id'];
$sellerid = $user_id;

$connect = OpenDbConnection();

//Query database to see whether user has seller credentials
$seller_check = "SELECT seller FROM users WHERE userID = $user_id";
$seller_status = SQLQuery($seller_check);
$seller_stat = $seller_status[0]["seller"]; 

if ($seller_stat == '0') {
header('Location: create_auction.php'); 
}

//Check whether user has any existing listings, if not redirect to create_auction.php
$sql2 = "SELECT * FROM auction_listing WHERE sellerUserID = '$sellerid' ORDER BY listingID desc";
$result2 = SQLQuery($sql2);
if (is_null($result2)) { ?>
<h2>You have to create an auction listing before you're able to view it!</h2>
<h3><a href="create_auction.php"> Click here to list an item!</a></h3>
<?php die(); } ?>


<div class="container">

<h2 class="my-3">My listings</h2>

<?php


//Queries to gather and display relevant information about current user's listings


//Selects full name of current user
$sql3 = "SELECT fName, lName FROM users WHERE userID = '$sellerid'";
$sellername = SQLQuery($sql3);
$fullname = ($sellername[0]['fName'])." ".($sellername[0]['lName']);



foreach ($result2 as $row) {
	$lid = ($row['listingID']); 
	$iname = ($row['itemName']);
	$ides = ($row['itemDescription']);
	($row['startPrice']);
	$endt = ($row['endTime']);
	$iimage = ($row['itemImage']);
	

	//Selects category name for each item
	$catid = ($row['categoryID']);	
	$sql4 = "SELECT subCategoryName FROM categories WHERE categoryID = '$catid'";
	$result4 = SQLQuery($sql4);
	$catname = $result4[0]['subCategoryName'];

	//Displays relevant timing information for each listing
	$end_time = DateTime::createFromFormat('Y-m-d H:i:s', $endt);
    $now = new DateTime();
    if ($now > $end_time) {
        $time_remaining = 'This auction has ended';
    }
    else {
        // Get interval:
        $time_to_end = date_diff($now, $end_time);
        $time_remaining = display_time_remaining($time_to_end);
    }


	echo "<div class='item'>";
	echo "<div class='item_row'>Listing ID: $lid</div>";
	echo "<div class='item_row'>Seller Name: $fullname</div>";
	echo "<div class='item_row'>Item Name: $iname</div>";
	echo "<div class='item_row'>Item Description: $ides</div>";
	echo "<div class='item_row'>Category: $catname</div>";
	echo "<div class='item_row'>Time Remaining: $time_remaining</div>";
	?>
	<?php if($iimage):?>
	<div class="img-block">
        <img src="<?php echo($iimage); ?>" width="300" height="200" class="img-responsive"/>
    <?php   endif; 
	echo "<br>"; 
}




CloseDbConnection($connect);

  
?>

<?php include_once("footer.php")?>