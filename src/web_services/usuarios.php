<?php
require_once '../clases/Conector.php';
require_once '../clases/TipoDocumento.php';
require_once '../clases/Usuario.php';
require_once '../clases/Rol.php';
$filtro = '';
foreach ($_POST as $key => $value) {
    if($filtro!=null) $filtro.=' and ';
    if($key!='texto') $filtro.="$key $value";
    else $filtro .= "(lower(usuario) like '%$value%' or lower(nombres) like '%$value%' or lower(apellidos) like '%$value%' or lower(id_rol) like '%$value%')";
}
if($filtro==null) $filtro = 'usuario is not null';
$filtro .= ' limit 50';
$datos = Usuario::getListaEnObjetos($filtro);
$json = '[';
foreach ($datos as $objeto) {
    $json .= '{"id":'.$objeto->getId().', "tipo_documento":"'.$objeto->getTipo_documento()->getId().'", "documento":"'.$objeto->getDocumento().'", "nombres":"'.$objeto->getNombres_completos().'","rol":"'.$objeto->getRol()->getNombre().'","correo":"'.$objeto->getCorreo().'","telefono":"'.$objeto->getTelefono().'","idusuario":"'.$objeto->getIdUsuario().'"},';
}$json = trim($json,',').']';
echo $json;