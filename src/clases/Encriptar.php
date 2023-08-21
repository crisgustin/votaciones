<?php
class Encriptar {
    private $method;
    private $clave;
    private $iv;
    
    public function __construct() {
        $this->method = ' AES-128-CBC';
        $this->clave = '9p7crxj5';
        $this->iv = $this->getIV();
    }

    private function getIV() {
        //print_r(openssl_get_cipher_methods());
        //return base64_encode(openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->method)));
        return openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->method));
    }
    
    public function encriptar($cadena) {
        return openssl_encrypt($cadena, $this->method, $this->clave, false, $this->iv);
    }
    
    public function desencriptar($cadena) {
        return openssl_decrypt($cadena, $this->method, $this->clave, false, $this->iv);
    }
}
