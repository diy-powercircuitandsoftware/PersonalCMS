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

    public function GetBlogFile($id, $mode = Blog_Database::Access_Public) {
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

    public function SearchBlogUsingKeywordID($id, $startat, $mode = Blog_Database::Access_Public) {
        $data = array();
        $public = " ";
        if ($mode == Blog_Database::Access_Public) {
            $public = "AND public=1 ";
        }
        $stmt = $this->bd->prepare(
                'SELECT blog.id,blog.title,blog.description FROM blog '
                . 'INNER JOIN blogcategory '
                . 'ON blog.id=blogcategory.blogid '
                . 'WHERE  blog.enable=1 '
                . 'AND blog.id>:id '
                . 'AND blogcategory.keywordid=:keywordid '
                . $public
                . 'ORDER BY blog.id ASC LIMIT 30; ');
        $stmt->bindParam(':id', $startat, SQLITE3_INTEGER);
        $stmt->bindParam(':keywordid', $id, SQLITE3_INTEGER);
        $results = $stmt->execute();
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }

}
