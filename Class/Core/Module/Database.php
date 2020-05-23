<?php

/**
 * Description of Database
 *
 * @author annopnod
 */
class Module_Database extends SQLite3 {

    

    public $ModulePath = "";

    public function __construct(Config $cfg) {
        $this->ModulePath = $cfg->GetDataPath() . "/Module/";
        if (!is_dir($this->ModulePath)) {
            mkdir($this->ModulePath);
        }

        $this->open($this->ModulePath . "Module.db");
    }

    public function GetTableFields($table) {
        $out = array();
        $results = $this->query("PRAGMA table_info('" . $table . "')");
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $out[] = $row;
        }
        return $out;
    }

    public function GetTableList() {
        $out = array();
        $results = $this->query(" SELECT name FROM sqlite_master WHERE type='table';");
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $out[] = $row["name"];
        }
        return $out;
    }

    public function AddModule($dirname, $classname, $public,$priority) {
        try {
            $stmt = $this->prepare("INSERT INTO module (dirname,classname,public,priority) VALUES ( :dirname,:classname,:layout)");
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

    public function LoadModule($Layout = self::Layout_None) {
        $pointer = $this->pdo->GetPointer();
        $sql = "SELECT  dirname,classname FROM module "
                . "WHERE enable=1 AND public=1 "
                . "AND layout =:layout  ORDER BY priority ASC ";
        $stmt = $pointer->prepare($sql);
        $stmt->bindParam(':layout', $Layout);

        return $stmt->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    public function LoadModuleMember($Layout = self::Layout_None) {
        $pointer = $this->pdo->GetPointer();
        $sql = "SELECT  dirname,classname   FROM module "
                . "WHERE enable=1 AND layout =:layout  ORDER BY priority ASC ";
        $stmt = $pointer->prepare($sql);
        $stmt->bindParam(':layout', $Layout);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

}
