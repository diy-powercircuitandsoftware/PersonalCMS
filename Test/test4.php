
<?php
//https://www.codegrepper.com/code-examples/php/encryption+and+decryption+in+php+example
include_once '../Class/Cryptography/AES.php';
$aes=new Cryptography_AES("ss");
$e= ( $aes->Encrypt("Welcome to GeeksforGe", "GeeksforGeeks"));
echo $aes->Decrypt($e, "GeeksforGeeks");