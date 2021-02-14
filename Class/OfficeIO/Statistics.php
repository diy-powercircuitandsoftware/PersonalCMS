<?php

class OfficeIO_Statistics {

    private $zip;

    function __construct($filename) {
        $this->zip = new ZipArchive();
        if (file_exists($filename)) {
            $this->zip->open($filename);
        } else {
            $this->zip->open($filename, ZipArchive::CREATE);
              $this->zip->addFromString("Metadata", serialize(array(
                "author" => "PersonalCMS@AnnopNod",
                "app" => "Statistics",
                "version" => "1",
                "date" => date("Y-m-d")
            )));
        }
        echo $filename;
    }

    function Close() {
        return $this->zip->close();
    }

     function AddCSV($path, $data = array()) {
       
        $this->zip->addFromString($this->Normalize($path), $this->Array2Csv($data));
    }

    
    function Array2Csv($data, $delimiter = ',', $enclosure = '"', $escape_char = "\\") {
        $f = fopen('php://memory', 'r+');
        foreach ($data as $item) {
            fputcsv($f, $item, $delimiter, $enclosure, $escape_char);
        }
        rewind($f);
        return stream_get_contents($f);
    }

   
    function Delete($path) {
        return $this->zip->deleteName($path);
    }

    function GetCSV($path) {
        return $this->zip->getFromName($path);
    }

    public function Normalize($Path) {
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
        return implode(DIRECTORY_SEPARATOR, $ArrayOut);
    }

}
