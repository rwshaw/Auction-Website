<?php include_once("header.php")?>
<?php 
require("utilities.php");
require_once("mysql_connect.php");
require("debug.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>



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

<?php
  // Retrieve these from the URL
  
  // Base search statement
  $base_query1 = "SELECT a.listingID, ItemName, ItemDescription, ifnull(max(bidPrice),startPrice) as currentPrice, count(bidID) as num_bids, endTime, c.deptName, c.subCategoryName from auction_listing a left join bids b on a.listingID = b.listingID left join categories c on a.categoryID = c.categoryID where endTime > now() ";
  $base_query2 = "group by a.listingID, a.ItemName, a.ItemDescription, a.endTime, c.deptName, c.subCategoryName ";
  $where_conditions = array();

  if (!isset($_GET['keyword'])) {
  }
  else {
    if (strlen($_GET["keyword"]) > 0) {
      $keyword = strtolower($_GET['keyword']); //remove case for wildcard search
      // $where_conditions[] = "AND lower(ItemName) like '%" .strtolower($keyword) . "'";
    }
    //if variable is null do nothing
  }

  if (!isset($_GET['cat'])) {
    // TODO: Define behavior if a category has not been specified.
  }
  else {
    $category = $_GET['cat'];
    if ($category != '') {
      if ($category == "Sport") {
        $category = "Sport & Leisure";
      }
      $where_conditions[] = "AND deptName = '" . $category . "'";
    }
    }
  
  if (!isset($_GET['order_by'])) {
    $ordering = "order by endTime"; //This is the default parameter at the moment.
  }
  else {
    $ordering = $_GET['order_by'];
    if ($ordering === "date") {
      $ordering = "order by endTime";
    }
    elseif ($ordering === "pricelow") {
      $ordering = "order by currentPrice asc";
    }
    elseif ($ordering === "pricehigh") {
      $ordering = "order by currentPrice desc";
    }
    else {} // do nothing for now.
  }

  // Page variables
  $results_per_page = 10;
  
  if (!isset($_GET['page'])) {
    $curr_page = 1;
  }
  else {
    $curr_page = $_GET['page'] ;
  }
  $limit = " LIMIT " .($curr_page -1)*$results_per_page . ", $results_per_page";

  //if keyword variable is set, then we will prepare query to prevent SQL injection.
  if (isset($keyword)) {
    $sq_no_limit = $base_query1 . "AND lower(ItemName) like ? " . implode(' ', $where_conditions) . ' ' . $base_query2 ; //for pagination, not need for ordering, dont want limit
    $search_query = $sq_no_limit . $ordering . $limit;
    $con = OpenDbConnection();
    $stmt = $con->stmt_init();
    $stmt->prepare($search_query);
    $wild_keyword = "%" . $keyword . "%";
    $stmt->bind_param("s", $wild_keyword);
    $stmt->execute();
    $search_result = $stmt->get_result();
  }
  else {   //if not set we can prepare query without using an itemName wildcard user input
    $sq_no_limit = $base_query1 . implode(' ', $where_conditions) . ' ' . $base_query2;
    $search_query = $sq_no_limit . $ordering . $limit;
    $con = OpenDbConnection();
    $search_result = $con->query($search_query);
  }

  // need to fix time remaining calculation issue in utlities
  $now = new DateTime();

  /* TODO: Use above values to construct a query. Use this query to 
     retrieve data from the database. (If there is no form data entered,
     decide on appropriate default value/default query to make. */
  
  /* For the purposes of pagination, it would also be helpful to know the
     total number of results that satisfy the above query */
  $num_results_query =  "SELECT COUNT(*) as num_rows FROM ($sq_no_limit) result";
  //if keyword - we need to prepare then execute, if not execute query immediately.
  if (isset($keyword)) {
    $stmt_num_rows = $con->stmt_init();
    $stmt_num_rows->prepare($num_results_query);
    $stmt_num_rows->bind_param("s", $wild_keyword);
    $stmt_num_rows->execute();
    $num_rows_result = $stmt_num_rows->get_result()->fetch_all(MYSQLI_ASSOC); 
  } else {
    $num_rows_result = SQLQuery($num_results_query);
  }
  // $num_results = SQLQuery($num_results_query);
  $num_results = $num_rows_result[0]['num_rows'] ?? false;
  $max_page = ceil($num_results / $results_per_page);
?>


<div class="container mt-5">

<!-- TODO: If result set is empty, print an informative message. Otherwise... -->

<ul class="list-group">

<!-- TODO: Use a while loop to print a list item for each auction listing
     retrieved from the query -->

<?php

  // List search/browse result.
  if ($search_result->num_rows>0) {
    while ($row = $search_result->fetch_all(MYSQLI_ASSOC)) {
      foreach($row as $item) {
        print_listing_li($item["listingID"],$item["ItemName"],$item["ItemDescription"],$item["currentPrice"],$item["num_bids"],$item["endTime"],$item["deptName"], $item["subCategoryName"]);
      }
    }
  }
  else { 
    // If no results returned - print alert
    $header = "Oooops...";
    $text1 = "Looks like there aren&#39;t any auctions that match your search criteria. If you can&#39;t find what you are looking for, try browsing by category?";
    $text2 = "Or try searching again!";
    print_alert("warning", $header, $text1, $text2);
  }

  //Close DB connection used for browse/search query, free result from memory
  $search_result->free_result();
  CloseDbConnection($con);

?>

</ul>

<!-- Pagination for results listings -->
<nav aria-label="Search results pages" class="mt-5">
  <ul class="pagination justify-content-center">
  
<?php

  // Copy any currently-set GET variables to the URL.
  $querystring = "";
  foreach ($_GET as $key => $value) {
    if ($key != "page") {
      $querystring .= "$key=$value&amp;";
    }
  }
  
  $high_page_boost = max(3 - $curr_page, 0);
  $low_page_boost = max(2 - ($max_page - $curr_page), 0);
  $low_page = max(1, $curr_page - 2 - $low_page_boost);
  $high_page = min($max_page, $curr_page + 2 + $high_page_boost);

  if ($max_page >0) {
    if ($curr_page != 1) {
      echo('
      <li class="page-item">
        <a class="page-link" href="browse.php?' . $querystring . 'page=' . ($curr_page - 1) . '" aria-label="Previous">
          <span aria-hidden="true"><i class="fa fa-arrow-left"></i></span>
          <span class="sr-only">Previous</span>
        </a>
      </li>');
    }
      
    for ($i = $low_page; $i <= $high_page; $i++) {
      if ($i == $curr_page) {
        // Highlight the link
        echo('
      <li class="page-item active">');
      }
      else {
        // Non-highlighted link
        echo('
      <li class="page-item">');
      }
      
      // Do this in any case
      echo('
        <a class="page-link" href="browse.php?' . $querystring . 'page=' . $i . '">' . $i . '</a>
      </li>');
    }
    
    if ($curr_page != $max_page) {
      echo('
      <li class="page-item">
        <a class="page-link" href="browse.php?' . $querystring . 'page=' . ($curr_page + 1) . '" aria-label="Next">
          <span aria-hidden="true"><i class="fa fa-arrow-right"></i></span>
          <span class="sr-only">Next</span>
        </a>
      </li>');
    }
  }


?>



  </ul>
<?php if ($max_page > 0) : ?>
  <div class="progress">
  <div class="progress-bar progress-bar-striped" role="progressbar" style="width: <?php echo ($curr_page/$max_page)*100 ?>%;" aria-valuenow="<?php echo ($curr_page/$max_page)*100 ?>" aria-valuemin="0" aria-valuemax="100"><?php echo "Page $curr_page of $max_page"?></div>
  </div>
<?php endif; ?>

<div></div>

</nav>


</div>



<?php include_once("footer.php")?>