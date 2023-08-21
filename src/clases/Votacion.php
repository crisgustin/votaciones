<?php

class Votacion {

    private $id;

    private $idVotante;

    
    public function __construct($campo, $valor) {

        if ($campo != null) {

            if (is_array($campo)) {

                foreach ($campo as $key => $value) $this->$key = $value;

            } else {

                $SQL = "select * from votacion where $campo = $valor;";
                //print_r($SQL);

                $resultado = Conector::ejecutarQuery($SQL);

                if ($resultado!=null&&count($resultado)>0) {

                    foreach ($resultado[0] as $key => $value) $this->$key = $value;

                }

            }

        }

    }

    

    public function getId() {

        return $this->id;

    }

    

    public function getIdVotante() {

        return $this->idVotante;

    }



    public function setId($id) {

        $this->id = $id;

    }

    

    public function setIdVotante($idVotante) {

        $this->idVotante = $idVotante;

    }

    public function grabar() {

       
            $SQL = "insert into votacion (idVotante) values ($this->idVotante);";
            //print_r($SQL);
            $resultado = Conector::ejecutarQuery($SQL);

    }


    

}