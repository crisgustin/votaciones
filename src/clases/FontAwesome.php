<?php
class FontAwesome {
    public $id,$icono;

    public function __construct($objeto=null,$id=null) {
        if($id!=null) $objeto = self::getListaEnObjetos("id = $id")[0];
        if($objeto==null) $objeto = self::getListaEnObjetos("id = 137")[0];
        foreach ($objeto as $key => $value) $this->$key = $value;
    }

    public function getId() {
        return $this->id;
    }

    public function getIcono() {
        return $this->icono;
    }

    private static function getLista($filtro=null) {
        $filtro = $filtro!=null?" where $filtro":'';
        $SQL = "select id, icono from fontawesome$filtro;";
        return Conector::ejecutarQuery($SQL);
    }

    public static function getListaEnObjetos($filtro=null){
        $datos = self::getLista($filtro);
        if($datos==null) $datos = [];
        $array = [];
        foreach ($datos as $objeto) {
            $array[] = new FontAwesome($objeto);
        } return $array;
    }

    public static function getOptionsHTML($predeterminado=null) {
        $html = '<option value="">Selecciona un icono</option>';
        $datos = self::getListaEnObjetos();
        foreach($datos as $objeto) {
            $selected = $predeterminado==$objeto->getId()?' selected':'';
            $html .= '<option value="'.$objeto->getId().'" data-icon="'.$objeto->getIcono().'"'.$selected.'>'.$objeto->getIcono().'</option>';
        } return $html;
    }
}