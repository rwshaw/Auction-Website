<?php 

  // require header and utilities 
  require_once("header.php"); 
  require_once("utilities.php"); 

  // Check if the user is already logged in, if yes then redirect him to welcome page
  if(isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {
    header("location: browse.php"); 
    exit; 
  }

  // initialise variables
  $fname                  = "";
  $lname                  = "";  
  $addressLine1           = "";
  $addressLine2           = "";
  $city                   = "";
  $postcode               = "";
  $email                  = "";
  $password               = "";
  $password_confirmation  = "";

  // initialise error variables
  $fnameErr                 = "";
  $lnameErr                 = "";  
  $addressLine1Err          = "";
  $addressLine2Err          = "";
  $cityErr                  = "";
  $postCodeErr              = "";
  $emailErr                 = "";
  $passwordErr              = "";
  $passwordConfirmationErr  = "";


  // check registration form has been submitted 
  if (isset($_POST['reg_user'])) {

    // first name server-side validation
      // check if field is empty, if so display error
    if (empty($_POST['fName'])) {
      $fnameErr = "* Please enter your first name."; 
    }
    else { // otherwise go ahead with creating variable 
      // pass to check_input and store as variable 
      $fname = check_input($_POST['fName']);
      // check input only contains letters 
      if (!preg_match("/^[a-z ,.'-]+$/i",$fname)) {
        $fnameErr = "* Please only use letters!";
      }
    }

    // last name server-side validation
      // check if field is empty, if so display error
    if (empty($_POST['lName'])) {
      $lnameErr = "* Please enter your last name."; 
    }
    else { // otherwise go ahead with creating variable 
      // pass to check_input and store as variable 
      $lname = check_input($_POST['lName']);
      // check input only contains letters 
      if (!preg_match("/^[a-z ,.'-]+$/i",$lname)) {
        $lnameErr = "* Please only use letters!";
      }
    }

    // address line 1 server-side validation
      // check if field is empty, if so display error
    if (empty($_POST['addressLine1'])) {
      $addressLine1Err = "* Please enter the first line of your address."; 
    }
    else { // otherwise go ahead with creating variable 
      // pass to check_input and store as variable 
      $addressLine1 = check_input($_POST['addressLine1']);
    }

    // address line 2 server-side validation
      // can be null, so no checking required 
    $addressLine2 = check_input($_POST['addressLine2']);


    // city server-side validation
      // check if field is empty, if so display error
    if (empty($_POST['city'])) {
      $cityErr = "* Please enter your city."; 
    }
    else { // otherwise go ahead with creating variable 
      // pass to check_input and store as variable 
      $city = check_input($_POST['city']); 
    }

    // post code server-side validation
      // check if field is empty, if so display error
    if (empty($_POST['postCode'])) {
      $postCodeErr = "* Please enter your post code."; 
    }
    else { // otherwise go ahead with creating variable 
      // pass to check_input and store as variable 
      $postcode = check_input($_POST['postCode']); 
    }

    // email server-side validation
      // check if field is empty, if so display error
    if (empty($_POST['register_email'])) {
      $emailErr = "* Please enter your email."; 
    }
    else { // otherwise go ahead with creating variable 
      // pass to check_input and store as variable 
      $email = check_input($_POST['register_email']); 
      // check input is a valid email address
      if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "* Please enter a valid email!";
      }
    } 

    // password server-side validation
      // check if field is empty, if so display error
    if (empty($_POST['register_password'])) {
      $passwordErr = "* Please enter your password."; 
    }
    else { // otherwise go ahead with creating variable 
      // pass to check_input and store as variable 
      $password = check_input($_POST['register_password']); 
      // check input is a valid password containing at least one uppercase, one lowercase, one number and one special character of between 8-30 characters.
        // set parameters (could also use regex here potentially)
      $uppercase = preg_match('@[A-Z]@', $password);
      $lowercase = preg_match('@[a-z]@', $password);
      $number    = preg_match('@[0-9]@', $password);
      $special_char = preg_match('@[^\w]@', $password);
        // test if password is missing some of the parameters above
      if(!$uppercase || !$lowercase || !$number || !$special_char || strlen($password) < 8) {
        $passwordErr = "* Please enter a password containing at least one uppercase, one lowercase, one number and one special character of between 8-30 characters.";
      }
    }  

    // password confirmation server-side validation
      // check if field is empty, if so display error
    if (empty($_POST['register_password_confirmation'])) {
      $passwordConfirmationErr = "* Please enter your password confirmation."; 
    }
    else { // otherwise go ahead with creating variable 
      // pass to check_input and store as variable 
      $password_confirmation = check_input($_POST['register_password_confirmation']); 
      // check input matches register_password
      if ($password_confirmation !== $password) {
        $passwordConfirmationErr = "* Please enter the same password as above.";
      } 
    }

    if (empty($fnameErr) && empty($lnameErr) && empty($addressLine1Err) && empty($cityErr) && empty($postCodeErr) && empty($emailErr) && empty($passwordErr) && empty($passwordConfirmationErr)) {

      // import sql connection script
      require_once('../mysql_connect.php'); 

      // open connection to database
      $db = OpenDbConnection(); 

      // check if user already exists with same email 
         // prepare sql statement
      $check_user_query = $db->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
        // bind parameters
      $check_user_query->bind_param("s",$email); 
        // execute prepared statement with binded parameters
      $check_user_query->execute(); 
        // retrieve result
      $result = $check_user_query->get_result(); 
        // fetch as associative array
      $user = $result->fetch_assoc();
        // check if user exists with this email address
      if ($user && $user['email'] == $email) { // if user does exist, redirect back to registration



          // replace this with cool alert 
          echo "user with this email already exists, please try again."; 
          header("refresh:3;url=register.php");
          exit;



        } 
      else { // if user doesn't exist 

          // hash password before storing in database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
          // insert user into database
            // prepare and bind parameters
        $stmt = $db->prepare("INSERT INTO users (fName, lName, email, password, addressLine1, addressLine2, city, postcode) VALUES(?,?,?,?,?,?,?,?)"); 
            // set parameters
        $stmt->bind_param("ssssssss", $fname, $lname, $email, $hashed_password, $addressLine1, $addressLine2, $city, $postcode); 
            // execute prepared statement with binded parameters
        $result = $stmt->execute(); 

        // if user inserted succesfully, notify user of success via email and redirect
        if ($result) {

          // get user_id and user_account_type and set session variables --> redirect user 
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
            // set variable for user_id 
          $user_id = $user['userID'];
            // close database connection
          CloseDbConnection($db); 

          // notify user via email
            // declare variables for PHP mailer 
          $subject = 'Congratulations! You are now registered.';
          $message = "<html>
                      <h2>Hi there!</h2>
                      <p>Thank you for joining the site.</p>
                      <p><em>Happy Selling!</em></p>
                      <p><em>The AuctionXpress Team</em></p></html>";
            // send mail
          send_user_email($user_id, $subject, $message);




          // if user_id succesfully retrieved
          if ($user_id) {
              // start sessions
            if (session_id() == "") {
              session_start();
            }
              // set session variables 
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $user_id;
            $_SESSION['account_type'] = "buyer";


            // replace this with cool alert 
              // tell user these have been set
            echo "Congratulations! You are now registered and will be redirected shortly."; 
              // redirect and exit
            header("refresh:3;url=index.php");
            exit; 
          }
        }
      }
    }
  }

    //   // AJAX to stop form from reloading if errors https://stackoverflow.com/questions/18343822/display-php-form-validation-results-on-same-page -->
    //   // header("location: process_registration.php");
    //   // exit;    
    // }

  // check user input (server-side)
  function check_input($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input; 
  }
?>

<!-- Page title -->
<div class="container">
<h2 class="my-3">Register new account</h2>

<!-- Create auction form -->
<form method="POST" action="" id="form">

  <!-- First name field -->
  <div class="form-group row">
    <label for="fName" class="col-sm-2 col-form-label text-right">First name</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="fName" placeholder="First name" name="fName" value = "<?php echo isset($_POST['fName']) ? $_POST['fName'] : '';?>">
      <small id="fnameHelp" class="form-text text-muted"><span class="text-danger"><?php echo $fnameErr;?></span></small>
    </div>
  </div>

    <!-- Last name field -->
  <div class="form-group row">
    <label for="lName" class="col-sm-2 col-form-label text-right">Last name</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="lName" placeholder="Last name" name="lName" value = "<?php echo isset($_POST['lName']) ? $_POST['lName'] : '';?>">
      <small id="lnameHelp" class="form-text text-muted"><span class="text-danger"><?php echo $lnameErr;?></span></small>
    </div>
  </div>

  <!-- Address line 1 field -->
  <div class="form-group row">
    <label for="addressLine1" class="col-sm-2 col-form-label text-right">Address Line 1</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="addressLine1" placeholder="Line 1" name="addressLine1" value = "<?php echo isset($_POST['addressLine1']) ? $_POST['addressLine1'] : '';?>">
      <small id="addressLine1Help" class="form-text text-muted"><span class="text-danger"><?php echo $addressLine1Err;?></span></small>
    </div>
  </div>

<!-- Address line 2 field -->
  <div class="form-group row">
    <label for="addressLine2" class="col-sm-2 col-form-label text-right">Address line 2</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="addressLine2" placeholder="Line 2" name="addressLine2" value = "<?php echo isset($_POST['addressLine2']) ? $_POST['addressLine2'] : '';?>">
    </div>
  </div>

<!-- City field -->
  <div class="form-group row">
    <label for="city" class="col-sm-2 col-form-label text-right">City</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="city" placeholder="City" name="city" value = "<?php echo isset($_POST['city']) ? $_POST['city'] : '';?>">
      <small id="cityHelp" class="form-text text-muted"><span class="text-danger"><?php echo $cityErr;?></span></small>
    </div>
  </div>

  <!-- Post Code field -->
    <div class="form-group row">
    <label for="postCode" class="col-sm-2 col-form-label text-right">Post Code</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="postCode" placeholder="Post code" name="postCode" value = "<?php echo isset($_POST['postCode']) ? $_POST['postCode'] : '';?>">
      <small id="postCodeHelp" class="form-text text-muted"><span class="text-danger"><?php echo $postCodeErr;?></span></small>
    </div>
  </div>

  <!-- Email field -->
    <div class="form-group row">
    <label for="register_email" class="col-sm-2 col-form-label text-right">Email</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="register_email" placeholder="Email" name="register_email" value = "<?php echo isset($_POST['register_email']) ? $_POST['register_email'] : '';?>">
      <small id="emailHelp" class="form-text text-muted"><span class="text-danger"><?php echo $emailErr;?></span></small>
    </div>
  </div>

  <!-- password field-->
  <div class="form-group row">
    <label for="register_password" class="col-sm-2 col-form-label text-right">Password</label>
    <div class="col-sm-10">
      <input type="password" class="form-control" id="register_password" placeholder="Password" name="register_password">
      <small id="passwordHelp" class="form-text text-muted"><span class="text-danger"><?php echo $passwordErr;?></span></small>
    </div>
  </div>

  <!-- password confirmation field -->
  <div class="form-group row">
    <label for="register_password_confirmation" class="col-sm-2 col-form-label text-right">Repeat password</label>
    <div class="col-sm-10">
      <input type="password" class="form-control" id="register_password_confirmation" placeholder="Enter password again" name="register_password_confirmation">
      <small id="passwordConfirmationHelp" class="form-text text-muted"><span class="text-danger"><?php echo $passwordConfirmationErr;?></span></small>
    </div>
  </div>

  <!-- submit registration button -->
  <div class="form-group row">
    <button type="submit" class="btn btn-primary form-control" name="reg_user">Register</button>
  </div>

<!-- end of form -->
</form>

<!-- Option for user in case they already have an account -->
<div class="text-center">Already have an account? <a href="" data-toggle="modal" data-target="#loginModal">Login</a>
</div>

<!-- Footer -->
<?php include_once("footer.php")?>
