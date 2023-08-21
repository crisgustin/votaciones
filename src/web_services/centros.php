<?php
require_once '../clases/Conector.php';
require_once '../clases/Centro.php';
$filtro = '';
foreach ($_POST as $key => $value) {
    if($filtro!=null) $filtro.=' and ';
    if($key!='texto') $filtro.="$key $value";
    else $filtro .= "(lower(id) like '%$value%' or lower(regiona) like '%$value%' or lower(centro) like '%$value%')";
}
if($filtro==null) $filtro = 'id is not null';
$filtro .= ' limit 10';
$datos = Centro::getListaEnObjetos($filtro);
$json = '[';
foreach ($datos as $objeto) {
    $json .= '{"id":'.$objeto->getId().', "regiona":"'.$objeto->getRegiona().'", "centro":"'.$objeto->getCentro().'"},';
}$json = trim($json,',').']';
echo $json;