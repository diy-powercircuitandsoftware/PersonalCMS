<?php

class Module_Exsimple extends Module_SDK_Basic {

    public function Execute(  $Layout = Module_SDK_Basic::Layout_None) {
        if ($Layout == Module_SDK_Basic::Layout_Nav||$Layout == Module_SDK_Basic::Layout_Aside ) {
            if ($this->UserID == NULL) {
                return "Welcome Guest";
            } else {
                return "Welcome UserID:" . $this->UserID;
            }
        }
    }

    public function GetTitle() {
        return "Exsimple";
    }

}
