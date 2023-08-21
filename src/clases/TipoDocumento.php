<?php

class TipoDocumento {

    private $id;

    private $nombre;

    private $descripcion;

    

    public function __construct($campo, $valor) {

        if ($campo != null) {

            if (is_array($campo)) {

                foreach ($campo as $key => $value) $this->$key = $value;

            } else {

                $SQL = "select id, nombre, descripcion from tipo_documento where $campo = '$valor';";

                $resultado = Conector::ejecutarQuery($SQL);

                if (count($resultado)>0) {

                    foreach ($resultado[0] as $key => $value) $this->$key = $value;

                }

            }

        }

    }

    

    public function getId() {

        return $this->id;

    }



    public function getNombre() {

        return $this->nombre;

    }

    

    public function getDescripcion() {

        return $this->descripcion;

    }

    

    public function setId($id) {

        $this->id = $id;

    }



    public function setNombre($nombre) {

        $this->nombre = $nombre;

    }



    public function setDescripcion($descripcion) {

        $this->descripcion = $descripcion;

    }

    

    public function grabar() {

        $this->descripcion = $this->descripcion==null?'null':"'$this->descripcion'";

        $SQL = "insert into tipo_documento (id, nombre, descripcion) values ('$this->id', '$this->nombre', '$this->descripcion');";

        Conector::ejecutarQuery($SQL);

    }

    

    public function modificar() {

        $this->descripcion = $this->descripcion==null?'null':"'$this->descripcion'";

        $SQL = "update tipo_documento set nombre = '$this->nombre', descripcion = $this->descripcion where id = '$this->id';";

        Conector::ejecutarQuery($SQL);

    }

    

    public function eliminar() {

        $SQL = "delete from tipo_documento where id = '$this->id';";

        Conector::ejecutarQuery($SQL);

    }



    public static function getLista($filtro){

        if ($filtro != null) $filtro=" where $filtro";

        $SQL = "select id, nombre, descripcion from tipo_documento$filtro order by nombre asc;";

        return Conector::ejecutarQuery($SQL);

    }

    

    public static function getListaEnObjetos($filtro){

        $datos = TipoDocumento::getLista($filtro);

        $datos = !is_array($datos)?array():$datos;

        $tipos = array();

        for ($i = 0; $i < count($datos); $i++) {

            $tipos[$i] = new TipoDocumento($datos[$i], null);

        }

        return $tipos;

    }

    

    public static function getOptionsHTML($predeterminado) {
        
        $datos = TipoDocumento::getListaEnObjetos(null);

        $html = '';

        foreach ($datos as $tipo) {

            if($predeterminado==null&&$tipo->getId()=='CC') $predeterminado='CC'; 

            $selected = $predeterminado==$tipo->getId()?' selected':'';

            $html.='<option value="'.$tipo->getId().'"'.$selected.'>'.$tipo->getNombre().'</option>';

        } return $html;

    }



    public static function getSiglas($nombre) {

        $nombre = mb_convert_case(Filter::drop_charset(Filter::clear($nombre)),MB_CASE_LOWER);

        $tipo = new TipoDocumento('lower(nombre)',$nombre);

        return $tipo->getId();

    }

}