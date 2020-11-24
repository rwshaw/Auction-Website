<?php include_once("header.php")?>
<?php include_once("mysql_connect.php")?>
<?php include_once("utilities.php")?> 


<?php
$connect = OpenDbConnection(); 
$user_id = $_SESSION['user_id'];
?>

<div class="container m-5 p-5">
<b>Before you can create an auction, it is necessary to update your account with the correct selling privileges. Please check the box to get authorisation!</b>
<form method="post" action="">
    <div class="form-check p-2 m-3">
<input class="form-check-input" type="checkbox" id="chkbox1" name="sellauth" value="authorisation"> Seller Authorisation
<small class="form-text text-muted">
  I agree to become a seller on AuctionXpress, and hereby agree to the terms and conditions.
</small>
</div>
<button type="submit" class="btn btn-primary form-control" name="submit">Submit</button>
</div>

<?php
//Update database to recognise seller credentials
if( empty($_POST["sellauth"]) ) { echo ""; }
else {
$connect = OpenDbConnection();
$statement = "UPDATE users SET seller = 1 WHERE userID = $user_id";
$connect->query($statement);


$_SESSION['account_type'] = "seller";

header("Location:create_auction.php"); }
?>



