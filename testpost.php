<?php

//move_uploaded_file($_FILES["test"]["tmp_name"],$_FILES["test"]["name"]);

if ($_POST["header"] == 206) {

    $fp = fopen(  $_FILES["test"]["name"],"a");
    fwrite($fp, file_get_contents($_FILES["test"]["tmp_name"]));
   
    fclose($fp);
 
}
