<?php

class UINAV {

    public function FindAllMenuFile($path) {
        $out = array();
        $filter = array(".", "..", "css", "js", "img", ".htaccess");
        foreach (array_diff(scandir($path), $filter) as $AValue) {
            $out[$AValue] = array();
            $subpath = $path . "/" . $AValue;
            foreach (array_diff(scandir($subpath), $filter) as $BValue) {
                $lastpath = $subpath . "/" . $BValue;
                if (is_file($lastpath) && pathinfo($lastpath, PATHINFO_EXTENSION) == "php") {
                    $dataout = array("name" => pathinfo($lastpath, PATHINFO_FILENAME), "path" => $lastpath);
                    $out[$AValue][] = $dataout;
                }
            }
        }
        return $out;
    }

    public function FindAllTemplate($path, $skip = array()) {
        $out = array();
        $filelist = array_diff(array_diff(scandir($path), array('.', '..')), $skip);
        foreach ($filelist as $value) {
            if (is_dir($path . $value)) {
                $out[$value] = $path . $value;
            }
        }
        return $out;
    }

    public function GetFilesList($path) {
        return array_diff(scandir($path), array(".", "..", ".htaccess"));
    }

}
