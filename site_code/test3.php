<?php 

$n = array(100,90,150,200,199,155,15,186);
rsort($n);
$top3 = array_slice($n, 0, 3);
foreach ($top3 as $key => $val) {
  echo "Values: $val Key: $key \n <br>";
}

// $n = array(100,90,150,200,199,155,15,186);
// rsort($n);
// $top3 = array_slice($n, 0, 3);
// echo 'Values: ';
// foreach ($top3 as $key => $val) {
//  echo "$val\n";
// }
// echo '<br>';
// echo 'Keys: ';
// foreach ($top3 as $key => $val) {
// echo "$key\n";
// }


?>