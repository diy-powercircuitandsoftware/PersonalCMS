<?php

class Module_Exsimple extends PersonalCMS_MOD_SDK {
    

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
