<?php
 

/**
 * Description of Session
 *
 * @author annopnod
 */
class User_Session {
    private $ud;

    public function __construct(User_Database $ud) {
        $this->ud = $ud;
    }
     public function Registered($sessionid) {
        $stmt = $this->ud->prepare("SELECT COUNT(*) AS c FROM session WHERE sessionid  = :sessionid AND  ipaddress=:ipaddress;");
        $stmt->bindParam(':sessionid', $sessionid);
        $stmt->bindParam(':ipaddress', $_SERVER["REMOTE_ADDR"]);
        $out =  $stmt->execute()->fetchArray(SQLITE3_ASSOC);
        return ( intval($out["c"]) == 1);
    }

    public function Register($sessionid, $userid) {
        $now = date('Y-m-d H:i:s');
        
        $stmt = $this->ud->prepare("INSERT INTO session (sessionid, ipaddress, accesstime, useragent,userid) VALUES (:sessionid, :ipaddress, :accesstime, :useragent,:userid);");
        $stmt->bindParam(':sessionid', $sessionid);
        $stmt->bindParam(':ipaddress', $_SERVER["REMOTE_ADDR"]);
        $stmt->bindParam(':accesstime', $now);
        $stmt->bindParam(':useragent', $_SERVER["HTTP_USER_AGENT"]);
        $stmt->bindParam(':userid', $userid);
        return $stmt->execute();
    }

    public function UnRegister($sessionid) {
   
        $stmt =$this->ud->prepare("DELETE   FROM session WHERE sessionid  = :sessionid  ");
        $stmt->bindParam(':sessionid', $sessionid);
        return $stmt->execute();
    }
}
