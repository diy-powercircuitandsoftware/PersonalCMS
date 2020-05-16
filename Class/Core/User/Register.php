<?php
 
/**
 * Description of Register
 *
 * @author annopnod
 */
class User_Register {
     private $ud;
    public function __construct(User_Database $ud) {
        $this->ud=$ud; 
    }

    public function Close(){
        $this->ud->close();
    }
   public function GetRegister($phone = 0) {
        $data = array();
        $stmt = $this->ud->prepare('SELECT phone,email,alias FROM register WHERE phone>:phone ORDER BY phone ASC LIMIT 32;');
        $stmt->bindValue(':phone', $phone, SQLITE3_TEXT);
        $results = $stmt->execute();
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
             $data[] = $row;
        }
        return $data;
    }
}
