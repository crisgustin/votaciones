<?php
require_once '../clases/Conector.php';
require_once '../clases/ListaEpps.php';
$filtro = '';
foreach ($_POST as $key => $value) {
    if($filtro!=null) $filtro.=' and ';
    if($key!='texto') $filtro.="$key $value";
    else $filtro .= "(lower(sacb) like '%$value%' or lower(tipo) like '%$value%' or lower(categoria) like '%$value%')";
}
if($filtro==null) $filtro = 'sacb is not null';
$filtro .= ' limit 10';
$datos = ListaEpps::getListaEnObjetos($filtro);
$json = '[';
foreach ($datos as $objeto) {
    $json .= '{"id":'.$objeto->getId().', "sibol":"'.$objeto->getSibol().'", "sacb":"'.$objeto->getSacb().'", "unspsc":"'.$objeto->getUnspsc().'","categoria":"'.$objeto->getCategoria().'","tipo":"'.$objeto->getTipo().'","unidad_medida":"'.$objeto->getUnidadMedida().'"},';
}$json = trim($json,',').']';
echo $json;