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
                echo "<option value=" . $row["deptName"] . ">" . $row["deptName"] . "</option>";
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

        <div class="col-12">
          <a data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
            Advanced search <i class="fa fa-angle-down"></i>
          </a>
        
        <div class="collapse" id="collapseExample">
          <div class="card card-body" style="max-height: 150px;">
            <div class="row">
              <div class="col-3">
              <label for="no_bids_less">No. bids less than</label>
                <input type="number" class="form-control" id="no_bids_less" min="0" max="100000" name="no_bids_less" placeholder="No. bids" >
              </div>
              <div class="col-3">
                <label for="no_bids_more">No. bids more than</label>
                <input type="number" class="form-control" id="no_bids_more" min="0" max="100000" name="no_bids_more" placeholder="No. bids">
              </div>
              <div class="col-3">
                <label for="price_more">Price more than</label>
                <input type="number" step="0.01" class="form-control" id="price_more" min="0" max="100000" name="price_more" placeholder="£">
              </div>
              <div class="col-3">
                <label for="price_less">Price less than</label>
                <input type="number" step="0.01" class="form-control" id="price_less" min="0" max="100000" name="price_less" placeholder="£">
              </div>
            </div>
          </div>
        </div>
        </div>

      </div>
    </form>
  </div>
  <!-- JS to retain form data in form options after input. -->
  <script type="text/javascript">
    document.getElementById("keyword").value = "<?php echo $_GET["keyword"]; ?>";
    document.getElementById("cat").value = "<?php echo $_GET["cat"]; ?>";
    document.getElementById("order_by").value = "<?php echo $_GET["order_by"]; ?>";
  </script>
  <!-- end search specs bar -->


</div>