<?php

/**
 * Description of GetBlog
 *
 * @author annopnod
 */
class Blog_Reader {

    private $bd;

    public function __construct(Blog_Database $bd) {
        $this->bd = $bd;
    }

     public function GetBlogFile($id,$mode = Blog_Database::Access_Public) {
        $data = array();
        $stmt = null;
        if ($mode == Blog_Database::Access_Member) {
            $stmt = $this->bd->prepare('SELECT * FROM blog WHERE  enable=1 ORDER BY id DESC LIMIT 30; ');
        } else {
            $stmt = $this->bd->prepare('SELECT * FROM blog WHERE enable=1 AND public=1 ORDER BY id DESC LIMIT 30; ');
        }
        $results = $stmt->execute();
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }
    
    public function GetLastBlogList($mode = Blog_Database::Access_Public) {
        $data = array();
        $stmt = null;
        if ($mode == Blog_Database::Access_Member) {
            $stmt = $this->bd->prepare('SELECT * FROM blog WHERE  enable=1 ORDER BY id DESC LIMIT 30; ');
        } else {
            $stmt = $this->bd->prepare('SELECT * FROM blog WHERE enable=1 AND public=1 ORDER BY id DESC LIMIT 30; ');
        }
        $results = $stmt->execute();
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }

   

}
