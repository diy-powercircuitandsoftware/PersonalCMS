<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SDK
 *
 * @author annopnod
 */
class Module_SDK_Basic {

    private $UserID = NULL;

    public function Config($cfg = array()) {
        return true;
    }

    public function ConfigForm() {
        return "";
    }

    public function Execute(Module_SDK_Layout $Layout = Module_SDK_Layout::Layout_None) {
        return true;
    }

    public function GetTitle() {
        return PersonalCMS_MOD_SDK::class;
    }

    public function SetUserID($UserID) {
        $this->UserID = $UserID;
    }

}
