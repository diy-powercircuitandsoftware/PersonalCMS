<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GetBlog
 *
 * @author annopnod
 */
class Event_Reader {
    private $ed;
    public function __construct(Event_Database $ed) {
        $this->ed=$ed;
    }
    
}
