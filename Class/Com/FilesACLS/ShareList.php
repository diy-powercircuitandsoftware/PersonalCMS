<?php

class FilesACLS_ShareList {

    private $fdb;

    public function __construct(FilesACLS_Database $fd) {
        $this->fdb = $fd;
    }

    public function AddShareList($Userid, $Path, $AccessMode) {
        $data = array();
        $stmt = $this->fdb->prepare('INSERT INTO  FilesACLS(userid,public,fullpath) VALUES(:userid,:public,:fullpath)  ');
        $stmt->bindValue(':userid', $Userid, SQLITE3_INTEGER);
        $stmt->bindValue(':public', $AccessMode, SQLITE3_INTEGER);
        $stmt->bindValue(':fullpath', $Path, SQLITE3_TEXT);
        $results = $stmt->execute();
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }

    public function Close() {
        $this->fdb->close();
    }

    public function GetShareListManager($userid) {
        $data = array();
        $stmt = $this->fdb->prepare('SELECT * FROM FilesACLS WHERE userid=:userid ; ');
        $stmt->bindValue(':userid', $userid, SQLITE3_INTEGER);
        $results = $stmt->execute();
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }

    public function GetRootShare( $id, $accessmode) {
        $stmt = null;
        if ($accessmode == FilesACLS_Database::Access_Member) {
            $stmt = $this->fdb->prepare('SELECT userid,fullpath FROM FilesACLS WHERE id=:id ; ');
        } else {
            $stmt = $this->fdb->prepare('SELECT userid,fullpath FROM FilesACLS WHERE id=:id  AND public=1; ');
        }
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $results = $stmt->execute();
        $row = $results->fetchArray(SQLITE3_ASSOC);
        if ($row) {
            return array("userid"=>$row["userid"],"fullpath"=>$row["fullpath"]);
        }
        return null;
    }

    public function GetShareList($userid, $accessmode) {
        $data = array();
        $stmt = null;
        if ($accessmode == FilesACLS_Database::Access_Member) {
            $stmt = $this->fdb->prepare('SELECT * FROM FilesACLS WHERE userid=:userid ; ');
        } else {
            $stmt = $this->fdb->prepare('SELECT * FROM FilesACLS WHERE userid=:userid AND public=1 ; ');
        }
        $stmt->bindValue(':userid', $userid, SQLITE3_INTEGER);
        $results = $stmt->execute();
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }

}
