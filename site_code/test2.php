<?php

// $left = [1=>true, 5=>true, 7=>true];
// $right = [6=>true, 7=>true, 8=>true, 9=>true];

// $union = $left + $right;
// $union2 = array_unique(array_merge($left, $right));
// $intersection = array_intersect_assoc($left, $right);

// echo "<pre>", var_dump($left, $right, $union, $intersection), "</pre>";

$left = [1, 5, 7];
$right = [6, 7, 8, 9];

// $union = $left + $right;
$union2 = array_unique(array_merge($left, $right));

// $intersection = array_intersect_assoc($left, $right);

// echo "<pre>", var_dump($left, $right, $union, $intersection), "</pre>";

echo "<pre>", var_dump($union2), "</pre>";




// $score = getSimilarity($left, $right);
// echo "<pre>", var_dump($score), "</pre>";

//   // function which returns similarity index using Jaccard Similarity 
//   function getSimilarity($user_bids, $others_bids) {
//     $union = $user_bids + $others_bids; 
//     $intersection = array_intersect_assoc($user_bids, $others_bids);
//     return $union;
//   }



?>