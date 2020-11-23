<?php include_once("header.php")?>
<?php include_once("mysql_connect.php")?>
<?php include_once("utilities.php")?> 

<div>
<b>Before you can create an auction, it is necessary to update your account with the correct selling privileges. Please check the box to get authorisation!</b>
</div>

<?php
$connect = OpenDbConnection(); 
$user_id = $_SESSION['user_id'];
?>

<form method="post" action="">
<input type="checkbox" name="sellauth" value="authorisation"> Seller authorisation
<button type="submit" class="btn btn-primary form-control" name="submit">Submit</button>

<?php
//Update database to recognise seller credentials
if( empty($_POST["sellauth"]) ) { echo "Please check box"; }
else {
$connect = OpenDbConnection();
$statement = "UPDATE users SET seller = 1 WHERE userID = $user_id";
$connect->query($statement);


$_SESSION['account_type'] = "seller";

header("Location:create_auction.php"); }
?>



