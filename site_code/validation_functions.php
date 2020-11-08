<?php 

	// validates presence of user input 
	// uses trim to get rid of whitespace (empty() considers "0" valid.)
	// "===" ensures the value is identical and has the same type. 
	// returns Boolean True if string is empty, nothing otherwise. 
	function is_empty($input) {
		return !isset($input) || trim($input) === ''; 
	}

	// validates length of user input is not too short
	// uses trim to ensure Spaces do not count 
	// returns Boolean True if string is smaller than allowed minimum
	function is_longer_than($input, $min) {
		$length = strlen(trim($input));
		return $length <= $min;
	}

	// validates length of user input is not too long
	// uses trim to ensure spaces do not count
	// returns Boolean True if input is longer than allowed maximum
	function is_shorter_than($input, $max) {
		$length = strlen(trim($input));
		return $length >= $max; 
	}

	// validates that user email includes certain characters
	// returns Boolean True if input does not include required characters
	// checks if email domain is included 
	// strpos returns position of first ocurrence of substring or false
	// uses !== to prevent position 0 from being considered false
	function includes_email_appendix($email, $required_string) {
		return !strpos($email, $required_string) !== false; //double negative? 
	}

  	// tests:
 	// echo (is_empty("")); 
	// echo is_longer_than("hello", 10);
	// echo is_shorter_than("hello", 4); 
	// echo includes_email_appendix("example@example.com",".com") 

	// read more about regex, don't fully udnerstand this bit yet, but useful for password and email format verification. 
	  // // has_valid_email_format('nobody@nowhere.com')
	  // // * validate correct format for email addresses
	  // // * format: [chars]@[chars].[2+ letters]
	  // // * preg_match is helpful, uses a regular expression
	  // //    returns 1 for a match, 0 for no match
	  // //    http://php.net/manual/en/function.preg-match.php
	  // function has_valid_email_format($value) {
	  //   $email_regex = '/\A[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}\Z/i';
	  //   return preg_match($email_regex, $value) === 1;

?>
