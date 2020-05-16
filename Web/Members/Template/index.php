<?php

$filelist = array_diff(scandir("."), array('.', '..'));
$ARRRND = array();
foreach ($filelist as $value) {
    if (is_dir($value)) {
        $ARRRND[] = $value;
    }
}
if (count($ARRRND) > 0) {
    header("location:  " . $ARRRND[rand(0, count($ARRRND) - 1)]);
} else {
    echo '<h1>No Template</h1><a style="float: right;" href="../../Root/index.php">Login</a>';
     
}

 
 