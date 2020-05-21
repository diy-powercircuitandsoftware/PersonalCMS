<?php
include_once '../../Class/SDK/Module/Basic.php';
include_once '../../Class/SDK/Module/Layout.php';
class Module_Exsimple extends Module_SDK_Basic {
    

    public function Execute($Layout) {
        if ($this->UserID == NULL) {
            return "Welcome Guest";
        } else {
            return "Welcome UserID:" . $this->UserID;
        }
    }

    public function GetTitle() {
        return "Exsimple";
    }

}
