<?php

class Cryptography_AES {

    private $iv;

    function __construct($iv) {
    $this->iv = substr(hash('sha512',$iv), 0, 16);  
    }

    public function Decrypt($cipher, $key) {
        return  openssl_decrypt($cipher, "AES-128-CTR",
                $key, 0, $this->iv);
    }

    public function Encrypt($plain, $key) {
      return  openssl_encrypt($plain, "AES-128-CTR",
                $key, 0, $this->iv);
    }

}
