<?php

class Mod_Exsimple {

    private $pdo;
    private $ModuleID = NULL;
    private $ModulePage = NULL;
    private $UserID = NULL;

    public function __construct(Com_Module_LoadModule $mod) {
        $this->pdo = $mod->GetPDO();
    }

    public function ConfigForm() {
        return "";
    }

    public function Execute() {
        if ($this->UserID == NULL) {
            return "Welcome Guest";
        } else {
            return "Welcome UserID:" . $this->UserID;
        }
    }

    public function GetTitle() {
        return "Exsimple";
    }

    public function SetModuleID($ModuleID) {
        $this->ModuleID = $ModuleID;
    }

    public function SetModulePage($ModulePage) {
        $this->ModulePage = $ModulePage;
    }

    public function SetUserID($UserID) {
        $this->UserID = $UserID;
    }

}
