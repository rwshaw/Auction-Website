<?php 

      error_reporting(E_ALL);
      ini_set('display_errors', '1');

      // import sql connection script
      require_once('mysql_connect.php'); 

      // open connection to database
      $db = OpenDbConnection(); 

      // 
      $email = "eric_zafarani@yahoo.co.uk";

      // get user_id and is_buyer and set session variables --> redirect user 
        // prepare sql statement to get user_id
      $user_id_query = $db->prepare("SELECT * from users WHERE email = ? LIMIT 1");
        // bind parameters
      $user_id_query->bind_param("s", $email); 
        // execute prepared statement with binded parameters
      $user_id_query->execute(); 
        // retrieve result
      $result = $user_id_query->get_result();
        // fetch as associative array
      $user = $result->fetch_assoc();
        // set variables for user's ID
      $user_id = $user['userID'];

        // close database connection
      CloseDbConnection($db); 

      // print user's account type
      echo $user_account_type;

?>