<?php include_once("header.php")?>

<div class="container">
<h2 class="my-3">Register new account</h2>

<!-- Create auction form -->
<form method="POST" action="process_registration.php">
  <div class="form-group row">
    <label for="FirstName" class="col-sm-2 col-form-label text-right">First name</label>
  <div class="col-sm-10">
      <input type="text" class="form-control" id="FirstName" placeholder="First name" name="FirstName">
      <small id="firstnameHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
  </div>
  </div>
    <div class="form-group row">
    <label for="LastName" class="col-sm-2 col-form-label text-right">Last name</label>
  <div class="col-sm-10">
      <input type="text" class="form-control" id="LastName" placeholder="Last name" name="LastName">
      <small id="LastnameHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
  </div>
  </div>
  <div class="form-group row">
    <label for="addressLine1" class="col-sm-2 col-form-label text-right">Address Line 1</label>
  <div class="col-sm-10">
      <input type="text" class="form-control" id="Addressline1" placeholder="Line 1" name="AddressLine1">
      <small id="AddressLine1Help" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
  </div>
  </div>
  <div class="form-group row">
    <label for="addressLine2" class="col-sm-2 col-form-label text-right">Address line 2</label>
  <div class="col-sm-10">
      <input type="text" class="form-control" id="Adressline2" placeholder="Line 2" name="AddressLine2">
      <small id="addressLine2Help" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
  </div>
  </div>
  <div class="form-group row">
    <label for="City" class="col-sm-2 col-form-label text-right">City</label>
  <div class="col-sm-10">
      <input type="text" class="form-control" id="City" placeholder="City" name="City">
      <small id="City" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
  </div>
  </div>
    <div class="form-group row">
    <label for="PostCode" class="col-sm-2 col-form-label text-right">PostCode</label>
  <div class="col-sm-10">
      <input type="text" class="form-control" id="PostCode" placeholder="PostCode" name="PostCode">
      <small id="PostCode" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
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
      <input type="password" class="form-control" id="passwordConfirmation" placeholder="Enter password again" name="password_confirmation">
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