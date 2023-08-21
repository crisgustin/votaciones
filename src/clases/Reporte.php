<?php

class Reporte {

    private $id;
    private $idEmergencia;
    private $dependencia;
    private $hora;
    private $descripcion;
    private $usuarioReportante;
    
    public function __construct($campo, $valor) {
        if ($campo != null) {
            if (is_array($campo)) {
                foreach ($campo as $key => $value) $this->$key = $value;
            } else {
                $SQL = "select * from reporte where $campo = $valor;";
                $resultado = Conector::ejecutarQuery($SQL);
                if ($resultado!=null&& is_array($resultado)) {
                    foreach ($resultado[0] as $key => $value) $this->$key = $value;
                }
            }
        }
    }
    
    public function getId() {
        return $this->id;
    }

    public function getIdEmergencia() {
        return $this->idEmergencia;
    }

    public function getDependencia(){
        return $this->dependencia;
    }

    public function getHora(){
        return $this->hora;
    }

    public function getDescripcion(){
        return $this->descripcion;
    }

    public function getUsuarioReportante(){
        return $this->usuarioReportante;
    }

    public function setId($id){
        $this->id = $id;
    }

    public function setIdEmergencia($idEmergencia){
        $this->idEmergencia = $idEmergencia;
    }

    public function setDependencia($dependencia){
        $this->dependencia = $dependencia;
    }

    public function setDescripcion($descripcion){
        $this->descripcion = $descripcion;
    }

    public function setHora($hora){
        $this->hora = $hora;
    }

    public function setUsuarioReportante($usuarioReportante){
        $this->usuarioReportante = $usuarioReportante;
    }

    
    public function grabar() {
        $SQL = "insert into reporte (idEmergencia, dependencia, hora, descripcion, usuarioReportante) values ($this->idEmergencia, '$this->dependencia', '$this->hora', '$this->descripcion', '$this->usuarioReportante');";
        //print_r($SQL);
        return Conector::ejecutarQuery($SQL);
    }

    
    public static function getLista($filtro){
        if ($filtro != null) $filtro=" where $filtro";
        $SQL = "select * from reporte$filtro;";
        return Conector::ejecutarQuery($SQL);
    }

    
    public static function getListaEnObjetos($filtro){
        $datos = Reporte::getLista($filtro);
        $datos = !is_array($datos)?array():$datos;
        $roles = array();
        for ($i = 0; $i < count($datos); $i++) {
            $roles[$i] = new Reporte($datos[$i], null);
        }
        return $roles;
    }

}