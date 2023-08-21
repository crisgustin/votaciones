<?php

class Candidato {

    private $idCandidato;

    private $nombres;

    private $apellidos;

    private $foto;

    private $votos;

    
    public function __construct($campo, $valor) {

        if ($campo != null) {

            if (is_array($campo)) {

                foreach ($campo as $key => $value) $this->$key = $value;

            } else {

                $SQL = "select * from candidatos where $campo = $valor;";
                //print_r($SQL);

                $resultado = Conector::ejecutarQuery($SQL);

                if ($resultado!=null&&count($resultado)>0) {

                    foreach ($resultado[0] as $key => $value) $this->$key = $value;

                }

            }

        }

    }

    

    public function getIdCandidato() {

        return $this->idCandidato;

    }

    

    public function getNombres() {

        return $this->nombres;

    }

    

    public function getNombres_completos() {

        return $this->nombres.' '.$this->apellidos;

    }



    public function getApellidos() {

        return $this->apellidos;

    }

    

    public function getVotos() {

        return $this->votos;

    }

    public function getFoto(){

        return $this->foto;
    }



    public function setIdCandidato($idCandidato) {

        $this->idCandidato = $idCandidato;

    }

    

    public function setNombres($nombres) {

        $this->nombres = $nombres;

    }



    public function setApellidos($apellidos) {

        $this->apellidos = $apellidos;

    }



    public function setVotos($votos) {

        $this->votos = votos;

    }



    public function setFoto($foto) {

        $this->foto = $foto;

    }

    public function grabar() {

       
            $SQL = "insert into candidatos values ('$this->idCandidato', '$this->nombres', '$this->apellidos', '$this->foto', 0);";
            //print_r($SQL);
            $resultado = Conector::ejecutarQuery($SQL);

    }

    

    public function modificar() {

        $this->telefono = $this->telefono!=null?"'$this->telefono'":'null';

        $SQL = "update candidatos set idCandidato = '$this->idCandidato', nombres = '$this->nombres', apellidos = '$this->apellidos' where idCandidato = '$this->idCandidato';";
        //print_r($SQL);
        return Conector::ejecutarQuery($SQL);

    }

    



    public static function getLista($filtro){

        if ($filtro != null) $filtro=" where $filtro";

        $SQL = "select * from candidatos$filtro;";

        return Conector::ejecutarQuery($SQL);

    }

    

    public static function getCount(){

        $SQL = "select count(idCandidato) as count from candidatos;";

        return Conector::ejecutarQuery($SQL)[0]['count'];

    }

    

    public static function getListaEnObjetos($filtro){

        $datos = Candidato::getLista($filtro);

        $datos = !is_array($datos)?array():$datos;

        $usuarios = array();

        for ($i = 0; $i < count($datos); $i++) {
            //print_r($datos[$i]);

            $usuarios[$i] = new Candidato($datos[$i], null);

        } return $usuarios;

    }

    

    /*public static function getOptionsHTML($predeterminado) {

        $html = '<option value="">Selecciona un Tipo de Proceso</option>';

        $filtro='id_rol=2';

        $datos = self::getListaEnObjetos($filtro);

        

        foreach ($datos as $objeto) {

            $selected = $predeterminado==$objeto->getId()?' selected':'';

            $html.='<option value="'.$objeto->getIdentificacion().'"'.$selected.'>'.$objeto->getNombres()." ".$objeto->getApellidos().'</option>';

        } return $html; 

    }*/



    

}