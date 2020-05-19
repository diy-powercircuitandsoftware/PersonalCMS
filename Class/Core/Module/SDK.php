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
class PersonalCMS_MOD_SDK {

    public const Layout_None = 0;
    public const Layout_Header = 1;
    public const Layout_Nav = 2;
    public const Layout_Article = 3;
    public const Layout_Aside = 4;
    public const Layout_Footer = 5;

    private $UserID = NULL;

    /* const Layout_Section_Header = 6;
      const Layout_Section_Nav = 7;
      const Layout_Section_Section = 8;
      const Layout_Section_Aside = 9;
      const Layout_Section_Footer = 10;
     */

    public function ConfigForm() {
        return "";
    }

    public function Execute($Layout = PersonalCMS_MOD_SDK::Layout_None) {
        return true;
    }

    public function GetTitle() {
        return PersonalCMS_MOD_SDK::class;
    }

    public function SetUserID($UserID) {
        $this->UserID = $UserID;
    }

}
