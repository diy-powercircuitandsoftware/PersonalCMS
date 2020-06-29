<?php

/**
 * Description of Member
 *
 * @author annopnod
 */
class User_Member {

    public const Auth_DatabaseError = 0;
    public const Auth_Complete = 1;
    public const Auth_NotRegistered = 2;
    public const Auth_PasswordError = 3;
    public const Auth_UserDisable = 4;

    private $ud;

    public function __construct(User_Database $ud) {
        $this->ud = $ud;
    }

    public function Close() {
        $this->ud->close();
    }

    public function AuthByPassword($userid, $password) {
        $hash = sha1(sha1("Transp" . $password . "arency"));
        $stmt = $this->ud->prepare('SELECT COUNT(id) AS n,enable,password  FROM user WHERE id=:id ; ');
        $stmt->bindValue(':id', $userid, SQLITE3_INTEGER);
        $results = $stmt->execute();
        $rs = $results->fetchArray(SQLITE3_ASSOC);
        if ($rs["n"] == 0) {
            return User_Member::Auth_NotRegistered;
        } else if ($rs["n"] == 1 && $rs["enable"] == 0) {
            return User_Member::Auth_UserDisable;
        } else if ($rs["n"] == 1 && $rs["enable"] == 1 && $hash !== $rs["password"]) {
            return User_Member::Auth_PasswordError;
        } else if ($rs["n"] == 1 && $rs["enable"] == 1 && $hash == $rs["password"]) {
            return User_Member::Auth_Complete;
        }
        return User_Member::Auth_DatabaseError;
    }

    public function GetProfileData($userid) {
        $stmt = $this->ud->prepare('SELECT id,alias,writable FROM user WHERE id=:id; ');
        $stmt->bindValue(':id', $userid, SQLITE3_INTEGER);
        $results = $stmt->execute();
        return $results->fetchArray(SQLITE3_ASSOC);
    }

    public function GetUserData($userid) {
        $stmt = $this->ud->prepare('SELECT id,alias,phone,email,address,addday,writable,enable FROM user WHERE id=:id; ');
        $stmt->bindValue(':id', $userid, SQLITE3_INTEGER);
        $results = $stmt->execute();
        return $results->fetchArray(SQLITE3_ASSOC);
    }

    public function GetUserList($startid = 0) {
        $data = array();
        $stmt = $this->ud->prepare('SELECT id,alias,enable,writable,email,phone FROM user WHERE id>:id LIMIT 32; ');
        $stmt->bindValue(':id', $startid, SQLITE3_INTEGER);
        $results = $stmt->execute();
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }

    public function EditUserData($userid, $param) {
        unset($param["id"]);
        $Prepare = $this->DataFilter($param);
        $sql = 'UPDATE user SET ' . implode("=?,", array_keys($Prepare)) . '=? WHERE id=:id; ';
        $stmt = $this->ud->prepare($sql);
        $stmt->bindParam(':id', $userid, SQLITE3_INTEGER);
        $val = array_values($Prepare);
        for ($i = 0; $i < count($val); $i++) {
            $stmt->bindParam($i + 1, $val[$i]);
        }
        $stmt->execute();
    }

    public function DataFilter($param) {
        $out = array();
        $results = $this->ud->query("PRAGMA table_info('user')");
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $name = $row["name"];
            if (isset($param[$name]) && $param[$name] !== "") {
                $v = null;
                if ($row["type"] == "INTEGER") {
                    $v = intval($param[$name]);
                } else if ($row["type"] == "BOOLEAN") {
                    if (( strtolower($param[$name]) == "true") || (strtolower($param[$name]) == "1") || ($param[$name] === true)) {
                        $v = 1;
                    } else {
                        $v = 0;
                    }
                } else {
                    $v = $param[$name];
                }

                $out[$name] = $v;
            }
        }
        return $out;
    }

    public function SearchUser($searchdata, $field = "alias") {
        /*
          $stmt = $this->prepare("SELECT * FROM keyword WHERE name LIKE :kw;");
          $stmt->bindValue(':kw', $k . "%", SQLITE3_TEXT);
          $results = $stmt->execute();
         */



        $data = array();
        $results = $this->ud->query('SELECT id,alias,enable,writable,email,phone FROM user WHERE ' . $field . ' LIKE "' . $searchdata . '"   ');
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }

    public function CanWritable($userid) {
        $stmt = $this->ud->prepare('SELECT COUNT(id) AS n  FROM user WHERE id=:id AND writable=1 AND enable=1; ');
        $stmt->bindValue(':id', $userid, SQLITE3_INTEGER);
        $results = $stmt->execute();
        $rs = $results->fetchArray(SQLITE3_ASSOC);
        return $rs["n"] == 1;
    }

}
