<?php include_once("header.php")?>

<div class="container">
<script defer src="form_validation.js"></script>
<h2 class="my-3">Register new account</h2>

<div id="error"></div>

<!-- Create auction form -->
<form method="POST" action="process_registration.php" id="form">

  <div class="form-group row">
    <label for="fName" class="col-sm-2 col-form-label text-right">First name</label>
  <div class="col-sm-10">
      <input type="text" class="form-control" id="fName" placeholder="First name" name="fName">
      <small id="fNameHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
  </div>
  </div>


    <div class="form-group row">
    <label for="lName" class="col-sm-2 col-form-label text-right">Last name</label>
  <div class="col-sm-10">
      <input type="text" class="form-control" id="lName" placeholder="Last name" name="lName">
      <small id="lNameHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
  </div>
  </div>
  <div class="form-group row">
    <label for="AddressLine1" class="col-sm-2 col-form-label text-right">Address Line 1</label>
  <div class="col-sm-10">
      <input type="text" class="form-control" id="AddressLine1" placeholder="Line 1" name="AddressLine1">
      <small id="AddressLine1Help" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
  </div>
  </div>
  <div class="form-group row">
    <label for="AddressLine2" class="col-sm-2 col-form-label text-right">Address line 2</label>
  <div class="col-sm-10">
      <input type="text" class="form-control" id="AddressLine2" placeholder="Line 2" name="AddressLine2">
  </div>
  </div>
  <div class="form-group row">
    <label for="city" class="col-sm-2 col-form-label text-right">City</label>
  <div class="col-sm-10">
      <input type="text" class="form-control" id="city" placeholder="City" name="city">
      <small id="city" class="form-text text-muted"><span class="text-danger" >* Required.</span></small>
  </div>
  </div>
    <div class="form-group row">
    <label for="postCode" class="col-sm-2 col-form-label text-right">Post Code</label>
  <div class="col-sm-10">
      <input type="text" class="form-control" id="postCode" placeholder="Post code" name="postCode">
      <small id="postCodeHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
  </div>
  </div>

  <div class="form-group row">
    <label for="email" class="col-sm-2 col-form-label text-right">Email</label>
	<div class="col-sm-10">
      <input type="text" class="form-control" id="email" placeholder="Email" name="email">
      <small id="emailHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
	</div>
  </div>

  <div class="form-group row">
    <label for="password" class="col-sm-2 col-form-label text-right">Password</label>
    <div class="col-sm-10">
      <input type="password" class="form-control" id="password" placeholder="Password" name="password">
      <small id="passwordHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
    </div>
  </div>
  <div class="form-group row">
    <label for="passwordConfirmation" class="col-sm-2 col-form-label text-right">Repeat password</label>
    <div class="col-sm-10">
      <input type="password" class="form-control" id="passwordConfirmation" placeholder="Enter password again" name="passwordConfirmation">
      <small id="passwordConfirmationHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
    </div>
  </div>
  <div class="form-group row">
    <button type="submit" class="btn btn-primary form-control" name="reg_user">Register</button>
  </div>
</form>

<div class="text-center">Already have an account? <a href="" data-toggle="modal" data-target="#loginModal">Login</a>
</div>

<?php include_once("footer.php")?>