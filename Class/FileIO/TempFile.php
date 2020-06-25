<?php

class TempFile {

    private $temppath;

    function __construct($path) {
        $this->temppath = $path . "/tmp/";
        if (!is_dir($this->temppath)) {
            mkdir($this->temppath);
        }
    }
 
    function mkdir($path) {
        if (!is_dir($this->temppath . $path)) {
            return mkdir($this->temppath . $path);
        }
        return true;
    }

    function fopen(...$args) {
        $args[0] = $this->temppath . $this->Normalize($args[0]);
        return fopen(...$args);
    }

    function fread(...$args) {
        return fread(...$args);
    }

    function fwrite(...$args) {
        return fwrite(...$args);
    }

    function ftell(...$args) {
        return ftell(...$args);
    }

    function fclose(...$args) {
        return fclose(...$args);
    }

    function file_exists(...$args) {
        return file_exists(...$args);
    }

    function getdiskpath($src) {
        return realpath($this->temppath . $this->Normalize($src));
    }

    function realpathsimulation($src) {
        return realpath($this->temppath) . $this->Normalize($src);
    }

    private function Normalize($Path) {
        $ArrayOut = array();
        $ReFormat = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, ($Path));
        $Split = array_filter(explode(DIRECTORY_SEPARATOR, $ReFormat), 'strlen');
        foreach ($Split as $value) {
            if ($value == "..") {
                array_pop($ArrayOut);
            } else if ($value !== ".") {
                $ArrayOut[] = $value;
            }
        }
        return DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $ArrayOut);
    }

}
