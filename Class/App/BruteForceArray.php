<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BruteForceArray
 *
 * @author annopnod
 */
class BruteForceArray {
    //put your code here
}

$charSet = '+-*/';
$maxLength = strlen($charSet);


$size = strlen($charSet);
$base = array();
$counter = 0;
$baseSize = 1;

// Let's see how many combinations exist for the given length and charset
$combinations = 0;
for($i=1;$i<=$maxLength;$i++) {
	$combinations += pow($size,$i);
} 
echo "There are $combinations possible combinations!<br/><br/>";

while($baseSize <= $maxLength) {
	// Go through all the possible combinations of last character and output $base
	for($i=0;$i<$size;$i++) {
		$base[0] = $i;
		for($j=$baseSize-1;$j>=0;$j--) {
			echo $charSet[$base[$j]];
		}
		echo '<br/>';	
	}
	// How many $base elements reached their max?
	for($i=0;$i<$baseSize;$i++) {
		if($base[$i] == $size-1) $counter++;
		else break;
	}
	// Every array element reached max value? Expand array and set values to 0. 
	if($counter == $baseSize) {
		// Notice <=$baseSize! Initialize 0 values to all existing array elements and ADD 1 more element with that value
		for($i=0;$i<=$baseSize;$i++) {
			$base[$i] = 0;
		}
		$baseSize = count($base);
	}
	// Carry one
	else {
		$base[$counter]++;
		for($i=0;$i<$counter;$i++) $base[$i] = 0;
	}

	$counter=0;
}
exit();