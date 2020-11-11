<?php include_once("header.php")?>
<?php include_once("mysql_connect.php")?>
<?php include_once("utitlities.php")?>



<?php
// (Uncomment this block to redirect people without selling privileges away from this page)
  // If user is not logged in or not a seller, they should not be able to
  // use this page.
  /* if (!isset($_SESSION['account_type']) || $_SESSION['account_type'] != 'seller') {
    echo "Before you can create an auction, it is necessary to update your account with the correct selling priveleges!";
    header('Location: browse.php'); */
  

?>

<div class="container">

<!-- Create auction form -->
<?php // if(!isset($_POST['submit'])) { ?>

<div style="max-width: 800px; margin: 10px auto">
  <h2 class="my-3">Create new auction</h2>
  <div class="card">
    <div class="card-body">
      <!-- Note: This form does not do any dynamic / client-side / 
      JavaScript-based validation of data. It only performs checking after 
      the form has been submitted, and only allows users to try once. You 
      can make this fancier using JavaScript to alert users of invalid data
      before they try to send it, but that kind of functionality should be
      extremely low-priority / only done after all database functions are
      complete. -->

      <!-- Checks whether submitted listing is a duplicate. GETs not working unless line 21 is uncommented-->
      <form method="post" action="create_auction_result.php">
      <?php 
      if (isset($_GET["auction_listing"]))
      {  
      if($_GET["auction_listing"] == "duplicate")
      {
      echo "<h4> Similar item has already been listed </h4>";
      echo "<br>";
      echo "<h4> Change title and try again </h4>";
      }

      else if($_GET["auction_listing"] == "succesful")
      {
      echo "<h4> Congratulations your listing has been succesful! </h4>";
      //die();
      }
      }
      else
      {
      echo "<h4> Create auction listing </h4>";
      }  
      ?>   


        <div class="form-group row">
          <label for="auctionTitle" class="col-sm-2 col-form-label text-right">Title of auction</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="auctionTitle" placeholder="e.g. Black mountain bike">
            <small id="titleHelp" style="color:red" class="form-text text-muted"><span class="text-danger">* Required.</span> </small>
          </div>
        </div>
        <div class="form-group row">
          <label for="auctionDetails" class="col-sm-2 col-form-label text-right">Details</label>
          <div class="col-sm-10">
            <textarea class="form-control" name="auctionDetails" rows="4"></textarea>
            <small id="detailsHelp"  class="form-text text-muted">Please enter a full description of your listing.</small>
          </div>
        </div>
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
        <div class="form-group row">

          <!--TODO: Throw up error message/prevent listing if date precedes current date-->
          <label for="auctionEndDate" class="col-sm-2 col-form-label text-right">End date</label>
          <div class="col-sm-10">
            <input type="datetime-local" class="form-control" name="auctionEndDate">
            <small id="endDateHelp" style="color:red" class="form-text text-muted"><span class="text-danger">* Required.</span> </small>
          </div>
        </div>
        <button type="submit" class="btn btn-primary form-control" name="submit">Create Auction</button>
      </form>
    </div>
  </div>
  <?php// } ?> 
</div>

</div>


<?php include_once("footer.php")?>