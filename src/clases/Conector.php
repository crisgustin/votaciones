<?php
class Conector {
    private $servidor;
    private $puerto;
    private $controlador;
    private $usuario;
    private $clave;
    private $bd;
    private $conexion;
    
    function __construct() {
        $this->servidor = 'localhost';
        $this->puerto = '3306';
        $this->controlador = 'mysql';
        //$this->puerto = '5432';
        //$this->controlador = 'pgsql';
        $this->usuario = 'cristian';
        $this->clave = '1utilizar9';
        $this->bd = 'votaciones';
    }
    
    private function conectar() {
        $options = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
        $this->conexion = new PDO("$this->controlador:host=$this->servidor;port=$this->puerto;dbname=$this->bd",$this->usuario, $this->clave,$options);
    }
    
    private function desconectar(){ $this->conexion = null; }
    
    public static function ejecutarQuery($SQL) {
        //print_r($SQL);
        $conector = new Conector();
        $consulta = null;
        try {
            $tipo = substr($SQL,0, strpos($SQL,' '));
            $conector->conectar();
            $conector->conexion->query("SET NAMES 'utf8';");
            $sentencia = $conector->conexion->prepare($SQL);
            $sentencia->execute();
            if($tipo=='select')$consulta=$sentencia->fetchAll();
            $sentencia->closeCursor();
        } catch (Exception $e){$consulta = $e->getMessage();}
        $conector->desconectar();
        return($consulta);
    }
}