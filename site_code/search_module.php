<div class="container">

<h2 class="my-3">Browse listings</h2>

<div id="searchSpecs">
<!-- When this form is submitted, this PHP page is what processes it.
     Search/sort specs are passed to this page through parameters in the URL
     (GET method of passing data to a page). -->
<form method="get" action="browse.php">
  <div class="row">
    <div class="col-md-5 pr-0">
      <div class="form-group">
        <label for="keyword" class="sr-only">Search keyword:</label>
	    <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text bg-transparent pr-0 text-muted">
              <i class="fa fa-search"></i>
            </span>
          </div>
          <input type="search" class="form-control border-left-0" id="keyword" name="keyword" placeholder="Search for a product">
        </div>
      </div>
    </div>
    <div class="col-md-3 pr-0">
      <div class="form-group">
        <label for="cat" class="sr-only">Search within:</label>
        <select class="form-control" id="cat" name="cat">
          <option value=''>All categories</option>
           <!-- TODO - Auto generate categories alphabetically in options from database -->
           <?php
           $sql = "SELECT distinct deptName from auctionsite.categories order by deptName";
           $result = SQLQuery($sql);
           foreach ($result as $row) {
             echo "<option value=" . $row["deptName"] .">" . $row["deptName"] ."</option>";
           }
           ?>
        </select>
      </div>
    </div>
    <div class="col-md-3 pr-0">
      <div class="form-inline">
        <label class="mx-2" for="order_by">Sort by:</label>
        <select class="form-control" id="order_by" name="order_by">
          <option value="date">Soonest expiry</option>
          <option value="pricelow">Price (low to high)</option>
          <option value="pricehigh">Price (high to low)</option>
          <option value="bids">Popularity</option>
        </select>
      </div>
    </div>
    <div class="col-md-1 px-0">
      <button type="submit" class="btn btn-primary">Search</button>
    </div>
  </div>
</form>
</div> 
<!-- JS to retain form data in form options after input. -->
<script type="text/javascript">
  document.getElementById("keyword").value="<?php echo $_GET["keyword"];?>";
  document.getElementById("cat").value="<?php echo $_GET["cat"];?>";
  document.getElementById("order_by").value="<?php echo $_GET["order_by"];?>";
</script>
<!-- end search specs bar -->


</div>