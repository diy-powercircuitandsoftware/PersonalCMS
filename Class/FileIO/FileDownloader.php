<?php

class FileDownloader {

    function __construct() {
        
    }

    private function SendMineType($filename) {
        header('Content-Disposition: attachment; filename=' .( pathinfo($filename, PATHINFO_FILENAME)) . "." . pathinfo($filename, PATHINFO_EXTENSION));
        header("Content-Type: " . mime_content_type($filename));
    }

    private function GetHttpRange($filesize) {
        if (isset($_SERVER['HTTP_RANGE'])) {
            $RangeString = explode('=', $_SERVER['HTTP_RANGE'], 2);
            $range = explode('-', $RangeString[1]);
            if (intval($range[1]) > 0) {
                return array(intval($range[0]), intval($range[1]));
            }
            return array(intval($range[0]), intval($filesize - 1));
        }
        return NULL;
    }

    private function SendPartialHeader($start, $end, $size) {
        $end=intval($end);
        $start=intval($start);
        $diff= ($end-$start)+1;
        header("Accept-Ranges: 0-" . strval( $end));
        header('HTTP/1.1 206 Partial Content');
        header("Content-Length: " . strval($diff));
        $ContRange = sprintf("Content-Range: bytes %d-%d/%d", $start, $end, $size);
        header($ContRange);
    }

    private function Stream($filename, $start, $end, $buffer = 8192) {
        $stream = fopen($filename, 'rb');
        fseek($stream, $start);
        while (!feof($stream) && ftell($stream) <= $end) {
            echo fread($stream, $buffer);
            flush();
        }
        fclose($stream);
    }

    private function SendAllByte($filename, $buffer = 8192) {
        $stream = fopen($filename, 'rb');
        while (!feof($stream)) {
            echo fread($stream, $buffer);
            flush();
        }
        fclose($stream);
    }

    private function SendHeader() {
        header('Content-Description: File Transfer');
        header('Content-Transfer-Encoding: binary');
        header('Connection: Keep-Alive');
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
    }

    public function DownloadFile($filename) {
        ob_clean();
        if (file_exists($filename) && is_readable($filename)) {
            $filesize = filesize($filename);
            $range = $this->GetHttpRange($filesize);
            $this->SendHeader();
            if ($range !== NULL) {
                $this->SendMineType($filename);
                $this->SendPartialHeader($range[0], $range[1], $filesize);
                $this->Stream($filename, $range[0], $range[1]);
            } else {
                header("Content-Length: " . $filesize);
                $this->SendMineType($filename);
                $this->SendAllByte($filename);
            }
            header('HTTP/1.1 200 OK');
        } else if (file_exists($filename) && !is_readable($filename)) {
            header('HTTP/1.0 403 Forbidden');
        } else {
            header('HTTP/1.0 404 Not Found');
        }
    }

}
 