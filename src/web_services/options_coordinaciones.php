<?php
require_once '../clases/Conector.php';
require_once '../clases/Coordinacion.php';
$filtro = '';
if (isset($_POST['oferta'])) $filtro .= " and oferta = '{$_POST['oferta']}'";
if (isset($_POST['modalidad'])) $filtro.=" and modalidad = '{$_POST['modalidad']}'";
$sql = "select coordinacion from ficha_programa where coordinacion is not null$filtro group by coordinacion;";
$data = Conector::ejecutarQuery($sql);
$html = '<option value="">Selecciona una coordinación</option>';
foreach ($data as $obj) {
    $value=$obj['coordinacion'];
    $coord = new Coordinacion('id',$value);
    $html .= '<option value="'.$value.'">'.$coord->getNombre().'</option>';
}
echo $html;