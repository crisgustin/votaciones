<?php

class Menu {

    private $id;

    private $nombre;

    private $descripcion;

    private $id_icono;

    private $ruta;

    private $id_menu;

            

    public function __construct($campo, $valor) {

        if ($campo != null) {

            if (is_array($campo)) {

                foreach ($campo as $key => $value) $this->$key = $value;

            } else {

                $SQL = "select id, nombre, descripcion, ruta, id_menu, id_icono from menu where $campo = $valor;";
                //print_r($SQL);
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



    public function getId_icono() {

        return $this->id_icono;

    }

    

    public function getFontAwesome() {

        return new FontAwesome(null,$this->id_icono);

    }

    

    public function getRuta() {

        return $this->ruta;

    }

    

    public function getId_menu() {

        return $this->id_menu;

    }

    

    public function getMenu() {

        return new Menu('id', $this->id_menu);

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

    

    public function setId_icono($id_icono) {

        $this->id_icono = $id_icono;

    }



    public function setRuta($ruta) {

        $this->ruta = $ruta;

    }

    

    public function setId_menu($id_menu) {

        $this->id_menu = $id_menu;

    }

    

    public function grabar() {

        if($this->id_menu==null)$this->id_menu= 'null';

        $this->descripcion = $this->descripcion==null?'null':"'$this->descripcion'";

        $this->id_icono = $this->id_icono==null?'null':"'$this->id_icono'";

        $this->ruta = $this->ruta==null?'null':"'$this->ruta'";

        $SQL = "insert into menu (nombre, descripcion, ruta, id_menu, id_icono) values ('$this->nombre', $this->descripcion, $this->ruta, $this->id_menu, $this->id_icono);";

        return Conector::ejecutarQuery($SQL);

    }

    

    public function modificar() {

        if($this->id_menu==null)$this->id_menu= 'null';

        $this->descripcion = $this->descripcion==null?'null':"'$this->descripcion'";

        $this->id_icono = $this->id_icono==null?'null':"'$this->id_icono'";

        $this->ruta = $this->ruta==null?'null':"'$this->ruta'";

        $SQL = "update menu set nombre = '$this->nombre', descripcion = $this->descripcion, ruta = $this->ruta, id_menu = $this->id_menu, id_icono = $this->id_icono where id = $this->id;";

        return Conector::ejecutarQuery($SQL);

    }

    

    public function eliminar() {

        $SQL = "delete from menu where id = $this->id;";

        return Conector::ejecutarQuery($SQL);

    }

    

    public function ocupado() {

        if($this->ruta==null) $SQL = "select count(id_menu) as count from menu where id_menu = $this->id;";

        else $SQL = "select count(id_menu) as count from menu_rol where id_menu = $this->id;";

        $count = Conector::ejecutarQuery($SQL)[0]['count'];

        return $count>0;

    }



    public static function getLista($filtro){

        if ($filtro != null) $filtro=" where $filtro";

        $SQL = "select id, nombre, descripcion, ruta, id_menu, id_icono from menu$filtro;";

        return Conector::ejecutarQuery($SQL);

    }

    

    public static function getListaEnObjetos($filtro){

        $datos = Menu::getLista($filtro);

        $datos = !is_array($datos)?array():$datos;

        $menus = array();

        for ($i = 0; $i < count($datos); $i++) {

            $menus[$i] = new Menu($datos[$i], null);

        }

        return $menus;

    }

    

    public static function getOptionsHTML($predeterminado) {

        $html = '<option value="">Selecciona un menú</option>';

        $filtro = 'ruta is null';

        $datos = self::getListaEnObjetos($filtro);

        foreach ($datos as $objeto) {

            $selected = $predeterminado==$objeto->getId()?' selected':'';

            $html .= '<option value="'.$objeto->getId().'"'.$selected.'>'.$objeto->getNombre().'</option>';

        } return $html;

    }

}