<?php

/**
 * Description of User
 *
 * @author annopnod
 */
class User_Manager {

    private $ud;

    public function __construct(User_Database $ud) {
        $this->ud = $ud;
    }

    public function Close() {
        $this->ud->close();
    }

    public function AddUser($alias, $password, $name, $lastname) {
        try {
            $hash = sha1(sha1("Transp" . $password . "arency"));
            $stmt = $this->ud->prepare("INSERT INTO user (id,alias,password,enable,name,lastname) VALUES (null, :alias,:password,1,:name,:lastname)");
            $stmt->bindValue(':alias', $alias, SQLITE3_TEXT);
            $stmt->bindValue(':password', $hash, SQLITE3_TEXT);
            $stmt->bindValue(':name', $name, SQLITE3_TEXT);
            $stmt->bindValue(':lastname', $lastname, SQLITE3_TEXT);
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function DeleteUser($id) {
        try {
            if ($id !== "") {
                $stmt = $this->ud->prepare("DELETE FROM user WHERE id IN (" . $this->FilterNumberSQL($id) . ")");
                $stmt->execute();
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function FilterNumberSQL($obj) {
        $Arrprocess = array();
        if (is_array($obj)) {
            $Arrprocess = $obj;
        } else {
            $Arrprocess = explode(",", $obj);
        }

        $out = array();
        foreach ($Arrprocess as $value) {
            if (is_numeric($value)) {
                $out[] = $value;
            }
        }
        return join(",", $out);
    }

}
