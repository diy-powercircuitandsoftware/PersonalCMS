<?php

class Validation {

    public function __construct() {
        
    }

    function ISFileName($filename) {
        return (preg_match("/^[a-zA-Z0-9-.!@ ]+$/", $filename) == 1) && (trim($filename) !== "");
    }

}
