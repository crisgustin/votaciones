<?php
require dirname(__FILE__) . './../../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

/*require 'path/to/PHPMailer/src/Exception.php';
require 'path/to/PHPMailer/src/PHPMailer.php';
require 'path/to/PHPMailer/src/SMTP.php';*/
class Mailer {
    private $mail;
    
    public function __construct() {
        $this->mail = new PHPMailer(true);
        $this->mail->SMTPDebug = SMTP::DEBUG_OFF;
        $this->mail->Host = 'desarrolloslope.com';
        $this->mail->isSMTP();
        $this->mail->CharSet = 'UTF-8';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'senanarino@desarrolloslope.com';
        $this->mail->Password = 'ZN5Bn@aIRrfd';
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = 587;
        $this->mail->setFrom('senanarino@desarrolloslope.com','Admin Centro Lope');
    }
    
    public function addEmail($email) {
        $this->mail->addAddress($email);
    }
    
    public function setAsunto($asunto) {
        $this->mail->Subject = $asunto;
    }
    
    public function setMensaje($mensaje,$html) {
        $this->mail->isHTML($html);
        $this->mail->Body = $mensaje;
    }
    
    public function addArchivo($ruta, $nombre) {
        $this->mail->addAttachment($ruta,$nombre);
    }
    
    public function addCopia($email,$tipo) {
        if($tipo!='CC')$this->mail->addBCC($email);
        else $this->mail->addCC($email);
    }
    
    public function enviar() {
        return $this->mail->send();
    }
}