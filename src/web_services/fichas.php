<?php
require_once '../clases/Conector.php';
require_once '../clases/Programa.php';
require_once '../clases/FichaPrograma.php';
require_once '../clases/NivelFormacion.php';
$filtro = '';$json = '[';
if(isset($_GET['texto'])) $filtro = " ficha = '{$_GET['texto']}'";
elseif(isset($_POST['id_programa'])) $filtro = "id_programa = {$_POST['id_programa']}";
if($filtro!='') {
    $datos = FichaPrograma::getListaEnObjetos($filtro);
    foreach ($datos as $ficha) {
        if(!isset($_POST['id_programa'])) $json.= '{"id":'.$ficha->getId().', "texto":"'.$ficha->getNombre().'"},';
        else $json.= '{"id":'.$ficha->getId().', "ficha":"'.$ficha->getFicha().'"},';
    }
}$json = trim($json, ','). ']';
echo $json;