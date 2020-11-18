<?php include_once("header.php")?>
<?php include_once("mysql_connect.php")?>
<?php include_once("utilities.php")?> 
<?php //session_start();
echo "Before you can create an auction, it is necessary to update your account with the correct selling priveleges. Please check box to get authorisation!"; 
$connect = OpenDbConnection(); 
echo $_SESSION['username'];
$user_id = $_SESSION['username'];
?>

<form method="post" action="">
<input type="checkbox" name="sellauth" value="authorisation">Seller authorisation
<button type="submit" class="btn btn-primary form-control" name="submit">Submit</button>

<?php
//Update database to recognise seller credentials
if( empty($_POST["sellauth"]) ) { echo "Please check box"; }
else {
$connect = OpenDbConnection();
$statement = "UPDATE users SET seller = 1 WHERE userID = $user_id";
$connect->query($statement);
header("Location:create_auction.php"); }
?>



