<?php
 
class FilesACLS_Database extends SQLite3 {

    public const Access_Public = 0;
    public const Access_Member = 1;

    private $path = "";
    private $ud=null;
    public function __construct(Config $cfg) {
        $this->path = $cfg->GetLocalConfigPath()["Com"] . "/Files/";
        if (!is_dir($this->path)) {
            mkdir($this->path);
        }
        $this->ud=$ud;
        $this->open($this->path . "Files.db");
    }

    public function GetUserDIR($userid) {
        return $this->ud->GetDataPath($userid);
    }

    public function Install() {
        $install = array();
        $install[0] = ('
    CREATE TABLE IF NOT EXISTS Files (
    id       INTEGER        PRIMARY KEY,
    userid   INTEGER,
    public   BOOLEAN,
    fullpath VARCHAR (1024) 
);');

        try {
            foreach ($install as $value) {
                $this->exec($value);
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function Uninstall() {
        try {
            $this->exec("DROP TABLE Files;");
            $this->exec("VACUUM;");
            return $this->close();
        } catch (Exception $e) {
            return false;
        }
    }

}
