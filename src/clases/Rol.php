<?php

class Rol {

    private $id;

    private $nombre;

    private $descripcion;

            

    public function __construct($campo, $valor) {

        if ($campo != null) {

            if (is_array($campo)) {

                foreach ($campo as $key => $value) $this->$key = $value;

            } else {

                $SQL = "select id, nombre, descripcion from rol where $campo = $valor;";

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

        $SQL = "insert into rol (nombre, descripcion) values ('$this->nombre', $this->descripcion);";

        return Conector::ejecutarQuery($SQL);

    }

    

    public function modificar() {

        $this->descripcion = $this->descripcion==null?'null':"'$this->descripcion'";

        $SQL = "update rol set nombre = '$this->nombre', descripcion = $this->descripcion where id = $this->id;";

        return Conector::ejecutarQuery($SQL);

    }

    

    public function eliminar() {

        $SQL = "delete from rol where id = $this->id;";

        return Conector::ejecutarQuery($SQL);

    }

    

    public function ocupado() {

        $SQL = "select (select count(id_rol) from menu_rol where id_rol = $this->id)+";

        $SQL .= "(select count(id_rol) from usuario where id_rol = $this->id) as count;";

        $count = Conector::ejecutarQuery($SQL)[0]['count'];

        return $count>0;

    }



    public static function getLista($filtro){

        if ($filtro != null) $filtro=" where $filtro";

        $SQL = "select id, nombre, descripcion from rol$filtro;";

        return Conector::ejecutarQuery($SQL);

    }

    

    public static function getCount(){

        $SQL = "select count(id) as count from rol;";

        return Conector::ejecutarQuery($SQL)[0]['count'];

    }

    

    public static function getListaEnObjetos($filtro){

        $datos = Rol::getLista($filtro);

        $datos = !is_array($datos)?array():$datos;

        $roles = array();

        for ($i = 0; $i < count($datos); $i++) {

            $roles[$i] = new Rol($datos[$i], null);

        }

        return $roles;

    }

    

    public static function getOptionsHTML($predeterminado) {

        $fil="id <> 11 order by id desc ";

        $datos = Rol::getListaEnObjetos($fil);

        $html = '<option value="">Selecciona un rol de usuario</option>';

        foreach ($datos as $rol) {

            $selected = $predeterminado==$rol->getId()?' selected':'';

            $html.='<option value="'.$rol->getId().'"'.$selected.'>'.$rol->getNombre().'</option>';

        } return $html;

    }

}