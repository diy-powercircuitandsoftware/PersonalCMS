<?php

/**
 * Description of Database
 *
 * @author annopnod
 */
class Module_Database extends SQLite3 {

    public $ModulePath = "";

    public const Access_Public = 1;
    public const Access_Member = 0;

    public function __construct(Config $cfg) {
        $this->ModulePath = $cfg->GetLocalConfigPath()["Core"] . "/Module/";
        if (!is_dir($this->ModulePath)) {
            mkdir($this->ModulePath);
        }
        $this->open($this->ModulePath . "Module.db");
         
    }

    public function AddModule($dirname, $classname, $public, $priority) {
        try {
            $stmt = $this->prepare("INSERT INTO module (dirname,classname,public,priority) VALUES ( :dirname,:classname,:public,:priority)");
            $stmt->bindValue(':dirname', $dirname, SQLITE3_TEXT);
            $stmt->bindValue(':classname', $classname, SQLITE3_TEXT);
            $stmt->bindValue(':public', $public, SQLITE3_INTEGER);
            $stmt->bindValue(':priority', $priority, SQLITE3_INTEGER);
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function LoadModule($mode) {
        $out = array();
        if (!$this->Installed()){
               return $out;
        }
        $results = null;
        if ($mode == Module_Database::Access_Member) {
            $results = $this->query("SELECT * FROM module ORDER BY priority ASC ");
        } else {
            $results = $this->query("SELECT * FROM module WHERE public=1 ORDER BY priority ASC ");
        }
        if ($results) {
            while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                $out[] = $row;
            }
        }
        return $out;
    }

    public function SearchModule($name) {
        $data = array();
        $results = null;
        if ($name !== "") {
            $results = $this->query('SELECT * FROM module WHERE classname LIKE "' . $name . '"  ; ');
        } else {
            $results = $this->query('SELECT * FROM module;');
        }

        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }

    public function Install() {
        $install = array();
        $install[0] = ('
    CREATE TABLE IF NOT EXISTS module (
    classname      VARCHAR (256) PRIMARY KEY,
    dirname       VARCHAR (256) NOT NULL,
    public     BOOLEAN,
    priority INTEGER);');
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
            $this->exec("DROP TABLE module;");

            $this->exec("VACUUM;");
            return $this->close();
        } catch (Exception $e) {
            return false;
        }
    }
     public function Installed() {
        $results = $this->query(" SELECT name FROM sqlite_master WHERE name='module';");
        return !($results->fetchArray() === false);
    }

}
