<?php include_once("header.php")?>
<?php include_once("mysql_connect.php")?>
<?php //session_start();?>
<div class="container my-5">

<?php


$connect = OpenDbConnection();
// This function takes the form data and adds the new auction to the database

// Requires seller to fill in every field apart from reserve price
if(empty($_POST["auctionTitle"]) || empty($_POST["auctionDetails"]) || empty($_POST["auctionCategory"]) || empty($_POST["auctionStartPrice"]) || empty($_POST["auctionEndDate"])) 
{
$value = "emptyform";
header("Location:create_auction.php?auction_listing=$value");
$connect->close();
}

// Data entered in create_auction.php form	
$ititle1 = $_POST["auctionTitle"];
$idesc1 = $_POST["auctionDetails"];
$icat = $_POST["auctionCategory"];
$stprice = $_POST["auctionStartPrice"];
$revprice = $_POST["auctionReservePrice"];
$enddate = ($_POST["auctionEndDate"]);
$enddate1 = date_create($_POST["auctionEndDate"]);
$datenow = time();
$image = $_POST["auctionImage"];


$ititle = htmlspecialchars(stripslashes($ititle1), ENT_QUOTES);
$idesc = htmlspecialchars(stripslashes($idesc1), ENT_QUOTES);

//Date input validation
if ($enddate1->getTimestamp() < $datenow) {
	$value = "dateerror";
header("Location:create_auction.php?auction_listing=$value");
$connect->close();
} 

//Prevents duplicates from being added 
$statement = "SELECT * FROM auction_listing WHERE itemName=?";
$stmt = $connect->prepare($statement);
$stmt->bind_param("s", $ititle);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows>=1)
{
$value = "duplicate";
header("Location:create_auction.php?auction_listing=$value");
$connect->close();
}

else
{

//Inserts form data into database
$statement = "INSERT INTO auction_listing(sellerUserID, itemName, itemDescription, startPrice, reservePrice, endTime, categoryID, itemImage) VALUES (?,?,?,?,?,?,?,?)";

$stmt = $connect->prepare($statement);


$user_id = $_SESSION['username'];


$stmt->bind_param("issddsis", $user_id, $ititle, $idesc, $stprice, $revprice, $enddate, $icat, $image);



$stmt->execute();


?>



<div class='fullscreenDiv'>
	<br>
    <div style="center"><big>Auction successfully created!<a href="mylistings.php"> View your new listing.</a></big></div>
    <br>
     <div style="center"><big><a href="create_auction.php">Or list another item!</a></big></div>
</div>​


<?php

}	


?>


		
		
           

</div>


<?php include_once("footer.php")?>