<?php

class UINAV {

    public function FindAllMenuFile($path) {
        $out = array();
        if (is_dir($path)) {
            foreach (new DirectoryIterator($path) as $mainfile) {
                if ($mainfile->isDir() && !$mainfile->isDot()) {
                    $out[$mainfile->getFilename()] = array();
                    foreach (new DirectoryIterator($mainfile->getPathname()) as $subfile) {
                        if ($subfile->getExtension() == "php") {
                            $out[$mainfile->getFilename()][] = array("path" => $mainfile->getFilename() . "/" . $subfile->getFilename(), "name" => $subfile->getBasename(".php"));
                        }
                    }
                }
            }
        }
        unset($out["css"]);
        unset($out["js"]);
        unset($out["img"]);
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

}
