<?php include_once("header.php")?>
<?php include_once("../mysql_connect.php")?>
<?php include_once("utilities.php")?> 

<?php //session_start(); 
$connect = OpenDbConnection(); 




$user_id = $_SESSION['user_id'];
//echo($user_id);


$seller_check = "SELECT seller FROM users WHERE userID = $user_id";
$seller_status = SQLQuery($seller_check);
$seller_stat = $seller_status[0]["seller"]; 

if ($seller_stat == '0') {
header('Location: auth.php'); 
}

?>



<!-- Create auction form -->
<?php if(!isset($_POST['submit'])) {
?>

<div class="container">
<div style="max-width: 800px; margin: 10px auto">
<?php if (!isset($user_id) or is_null($user_id)) { print_alert("danger","Oh no!", "You have be signed in to create auctions.", "Please sign in or sign up now"); echo '<hr class="my-2">';}?>
  <h1 class="my-3">Create New Auction</h1>
  <div class="card">
    <div class="card-body">
      

      <!-- Checks whether submitted listing is a duplicate-->
      <form method="post" action="create_auction_result.php">
      <?php 
      if (isset($_GET["auction_listing"]))
      {  
      if($_GET["auction_listing"] == "duplicate")
      {
      echo('<h4><div class="text-center" style="color:red">Similar item has already been listed.</div></h4>');
      echo "<br>";
      echo('<h4><div class="text-center" style="color:red">Please select a different title and try again!</div></h4>');
      }

      //Checks whether any required form sections left empty
      else if($_GET["auction_listing"] == "emptyform")
      {
      echo('<h4><div class="text-center" style="color:red">Required field(s) left empty. Please try again.</div><h4>');
      }

      //Checks whether date set prcedes current date
      else if($_GET["auction_listing"] == "dateerror")
      {
      echo('<h4><div class="text-center" style="color:red">Invalid date selected.</div><h4>');
      }
      }
      else
      {
      echo "<h5>Fill in the form below to list an item!</h5>";
      }  
      ?>   

      <!--Specify listing name-->
        <div class="form-group row">
          <label for="auctionTitle" class="col-sm-2 col-form-label text-right">Title of auction</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="auctionTitle" placeholder="Name of listing">
            <small id="titleHelp" style="color:red" class="form-text text-muted"><span class="text-danger">* Required.</span> </small>
          </div>
        </div>

        <!--Specify listing detials-->
        <div class="form-group row">
          <label for="auctionDetails" class="col-sm-2 col-form-label text-right">Details</label>
          <div class="col-sm-10">
            <textarea class="form-control" name="auctionDetails" rows="4"></textarea>
            <small id="detailsHelp" style="color:red"  class="form-text text-muted">* Required.</small>
          </div>
        </div>

        <!--Option to add image to listing-->
         <div class="form-group row">
          <label for="auctionImage" class="col-sm-2 col-form-label text-right">Image</label>
          <div class="col-sm-10">
            <input type="text" name="auctionImage">
          </div>
            <small id="imageHelp" class="form-text text-muted">Optional. Paste URL of image here.</small>
          </div>
       

        <!--Category selection-->
        <div class="form-group row">
          <label for="auctionCategory" class="col-sm-2 col-form-label text-right">Category</label>
          <div class="col-sm-10">
            <select class="form-control" name="auctionCategory">

              <?php // Populate category list with options from the database
              $sql = "SELECT categoryID, subCategoryName from categories order by subCategoryName";
              $result = SQLQuery($sql);

              echo "<option selected>Select category</option>";
              foreach ($result as $row) {
                echo "<option value=".$row["categoryID"].">".$row["subCategoryName"]."</option>";                 
              }            
              ?>

           </select>
            <small id="categoryHelp" style="color:red" class="form-text text-muted"><span class="text-danger">* Required.</span> </small>
          </div>
        </div>

        <!--Set initial price for listing-->
        <div class="form-group row">
          <label for="auctionStartPrice" class="col-sm-2 col-form-label text-right">Starting price</label>
          <div class="col-sm-10">
          <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">£</span>
              </div>
              <input type="number" class="form-control" name="auctionStartPrice">
            </div>
            <small id="startBidHelp" style="color:red" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
          </div>
        </div>
        <div class="form-group row">

          <!--Option to set reserve price-->
          <label for="auctionReservePrice" class="col-sm-2 col-form-label text-right">Reserve price</label>
          <div class="col-sm-10">
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">£</span>
              </div>
              <input type="number" class="form-control" name="auctionReservePrice">
            </div>
            <small id="reservePriceHelp" class="form-text text-muted">Optional. Auctions that end below this price will not go through. This value is not displayed in the auction listing.</small>
          </div>
        </div>

        <!--Specify end date of auction-->
        <div class="form-group row">
          <label for="auctionEndDate" class="col-sm-2 col-form-label text-right">End date</label>
          <div class="col-sm-10">
            <input type="datetime-local" class="form-control" name="auctionEndDate" placeholder="yyyy-mm-dd, HH:mm">
            <small id="endDateHelp" style="color:red" class="form-text text-muted"><span class="text-danger">* Required.</span> </small>
          </div>
        </div>
        <button type="submit" class="btn btn-primary form-control" name="submit" <?php echo (!isset($user_id) or is_null($user_id)) ? "disabled": "" ?> >Create Auction</button>
      </form>
      </div>
    </div>
  </div>
  <?php } ?> 
</div>



<?php include_once("footer.php")?>

<script type='text/javascript'>
  if (<?php echo (!isset($user_id) or is_null($user_id)) ? "true" : "false"; ?>) {
    $("#loginpopup").click();
  } 
</script>
