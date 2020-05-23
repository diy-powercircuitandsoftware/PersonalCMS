<?php

class Module_SDK_Basic {

    public const Layout_None = 0;
    public const Layout_Head = 1;
    public const Layout_Body = 2;
    public const Layout_Header = 3;
    public const Layout_Nav = 4;
    public const Layout_Article = 5;
    public const Layout_Aside = 6;
    public const Layout_Footer = 7;

    /* const Layout_Section_Header = 6;
      const Layout_Section_Nav = 7;
      const Layout_Section_Section = 8;
      const Layout_Section_Aside = 9;
      const Layout_Section_Footer = 10;
     */

    public $UserID = NULL;

    public function Config($cfg = array()) {
        return true;
    }

    public function ConfigForm() {
        return "";
    }

    public function Execute($Layout = Module_SDK_Basic::Layout_None) {
        return true;
    }

    public function GetTitle() {
        return PersonalCMS_MOD_SDK::class;
    }

    public function SetUserID($UserID) {
        $this->UserID = $UserID;
    }
     public function SupportLayout($Layout = Module_SDK_Basic::Layout_None) {
        return true;
    }

}
