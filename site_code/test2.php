<?php

// error_reporting(E_ALL);
// ini_set('display_errors', '1');

$user = array("1"=>"1", "2"=>"0", "3"=>"0");
$other = array("1"=>"1", "2"=>"0", "3"=>"0");
// $other = ("1=>0, 2=>1, 3=>0");

print_r($user); echo "<br>";
print_r($other); echo "<br>";

$intersection_count = 0;
$union_count = 0;

foreach (array_keys($user) as $key) {
	// if both users have bid on the item,  
	if ($user[$key] == $other[$key] && $user[$key] == "1" && $other[$key] == "1") {
		echo "1 ";
		$intersection_count += 1; 
	}
	if ($user[$key] + $other[$key] >= 1) {
		echo "0 ";
		$union_count += 1;
	}
}


$jaccard_similarity = $intersection_count / $union_count;

echo "<br>", ($intersection_count);
echo "<br>", ($union_count);
echo "<br>", ($jaccard_similarity); echo "<br><br>"; 


$result = jaccardSimilarlity($user, $other);

echo $result;

$similarity_score = array();

$similarity_score[] = $result;

echo "<pre>", print_r($similarity_score), "</pre>";



// https://helpful.knobs-dials.com/index.php/Similarity_or_distance_measures/metrics
// function to calculate jaccard similarlity as boolean intersection / boolean union
function jaccardSimilarlity ($user_array, $other_user_array) {

	// initialise counter variables for intersection and union
	$intersection_count = 0; 
	$union_count = 0;

	// loop through user array and the other user array
	foreach (array_keys($user_array) as $key) {
		// if both users have bid on the item (sum of pairwise multiplication)
		if ($user_array[$key] == $other_user_array[$key] && $user_array[$key] == "1" && $other_user_array[$key] == "1") {
			// increment intersection counter 
			$intersection_count += 1; 
		}
		// if at lease one user has bid on the item (sum of pairwise addition)
		if ($user_array[$key] + $other_user_array[$key] >= 1) {
			// increment union counter
			$union_count += 1;
		}
	}

	// caclulate jaccard similarlity value 
	$jaccard_similarity = $intersection_count / $union_count;
	
	// return jaccard similarlity value 
	return $jaccard_similarity;
}

// $left = [1=>true, 5=>true, 7=>true];
// $right = [6=>true, 7=>true, 8=>true, 9=>true];

// $union = $left + $right;
// $union2 = array_unique(array_merge($left, $right));
// $intersection = array_intersect_assoc($left, $right);

// echo "<pre>", var_dump($left, $right, $union, $intersection), "</pre>";

// $left = [1, 5, 7];
// $right = [6, 7, 8, 9];




// $union = $left + $right;
// $union2 = array_unique(array_merge($left, $right));

// $intersection = array_intersect_assoc($left, $right);

// echo "<pre>", var_dump($left, $right, $union, $intersection), "</pre>";

// echo "<pre>", var_dump($union2), "</pre>";




// $score = getSimilarity($left, $right);
// echo "<pre>", var_dump($score), "</pre>";

//   // function which returns similarity index using Jaccard Similarity 
//   function getSimilarity($user_bids, $others_bids) {
//     $union = $user_bids + $others_bids; 
//     $intersection = array_intersect_assoc($user_bids, $others_bids);
//     return $union;
//   }

// intersection = sum(RHS)
// 0 x 0 = 0
// 0 x 1 = 0
// 1 x 1 = 1

// union = sum (RHS) 
// 0 + 0 = 0 
// 0 + 1 = 1
// 1 + 1 = 1
















?>