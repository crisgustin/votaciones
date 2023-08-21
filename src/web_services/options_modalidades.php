<?php
require_once '../clases/Conector.php';
$filtro = isset($_POST['oferta'])?" and oferta = '{$_POST['oferta']}'":'';
$sql = "select modalidad from ficha_programa where modalidad is not null$filtro group by modalidad;";
$data = Conector::ejecutarQuery($sql);
$html = '<option value="">Selecciona una modalidad</option>';
foreach ($data as $obj) {
    $value=$obj['modalidad'];
    $key = $value=='P'?'Presencial':'Virtual';
    $html .= '<option value="'.$value.'">'.$key.'</option>';
}
echo $html;