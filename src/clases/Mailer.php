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
        $this->mail->Host = 'sistema.pruebassire119.com.co';
        $this->mail->isSMTP();
        $this->mail->CharSet = 'UTF-8';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'info@sistema.pruebassire119.com.co';
        $this->mail->Password = 'radamanth1s';
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = 587;
        $this->mail->setFrom('info@sistema.pruebassire119.com.co','Admin COE');
    }
    
    public function addEmail($email) {
        $this->mail->addAddress($email);
    }
    
    public function setAsunto($asunto) {
        $this->mail->Subject = $asunto;
    }
    
    public function setMensaje($mensaje,$html) {
        $this->mail->isHTML($html);
        $this->mail->Body = $mensaje.$this->firma();
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

    private function firma() {
        return '<br/><br/><strong style="font-size:14px;font-style:italic">SIRE - Sistema Integrado de Respuesta a Emergencias</strong>
                <p style="font-size:11px;font-style:italic">DGRD - BOMBEROS - POLICIA - TRANSITO - SALUD - SERVICIOS PUBLICOS</p>
                <p style="font-size:11px;font-style:italic">COE PASTO</p>
                <p style="font-size:11px;font-style:italic">Pasto, Nariño, Colombia</p>';
    }
}