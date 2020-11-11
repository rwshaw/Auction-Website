<?php include_once("header.php")?>
<?php include_once("create_auction.php")?>
<?php include_once("mysql_connect.php")?>
<?php // start_session();?>
<div class="container my-5">

<?php
//enabling start_session() seems to break the whole thing
//FIX HEADER



$connect = OpenDbConnection();
// This function takes the form data and adds the new auction to the database.

// Requires seller to fill in every field apart from reserve price
if(!empty($_POST["auctionTitle"]) && !empty($_POST["auctionDetails"]) && !empty($_POST["auctionCategory"]) && !empty($_POST["auctionStartPrice"]) && !empty($_POST["auctionEndDate"])) 

{

// Data entered in create_auction.php form	
$ititle = $connect->real_escape_string($_POST["auctionTitle"]);
$idesc = $connect->real_escape_string($_POST["auctionDetails"]);
$icat = $_POST["auctionCategory"];
$stprice = $_POST["auctionStartPrice"];
$revprice = $_POST["auctionReservePrice"];
$enddate = $_POST["auctionEndDate"];


//Seems to prevent duplicates from being added but no message displayed?
$statement = "SELECT * FROM auction_listing WHERE itemName=?";
$stmt = $connect->prepare($statement);
$stmt->bind_param("s", $ititle);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows>=1)
{
$value = "duplicate";
$connect->close();
header("Location:create_auction.php?auction_listing=$value");
}

else
{

//Inserts from data into database
$statement = "INSERT INTO auction_listing(sellerUserID, itemName, itemDescription, startPrice, reservePrice, endTime, categoryID) VALUES (?,?,?,?,?,?,?)";

$stmt = $connect->prepare($statement);

/* $_SESSION['email'] = $email;
$sql = "SELECT userID FROM users WHERE email = '$email'";
$userid = SQLQuery($sql); */

//TEMPORARY REPLACE WITH CORRECT SESSION VARIABLE
$temp = 1;


$stmt->bind_param("issddsi", $temp, $ititle, $idesc, $stprice, $revprice, $enddate, $icat);



$stmt->execute();

$value="successful";
$connect->close();


echo('<div class="text-center">Auction successfully created!<a href="mylistings.php">View your new listing.</a></div>');

header("Location:create_auction.php?auction_listing=$value");




}	
}

else
{
header("Location:create_auction.php");
} 

CloseDbConnection($connect); 
?>


		
		
            



</div>


<?php include_once("footer.php")?>