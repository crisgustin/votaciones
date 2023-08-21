<?php
require_once '../clases/Conector.php';
require_once '../clases/Programa.php';
$filtro='';
if(isset($_POST['oferta']))$filtro .= " and oferta = '{$_POST['oferta']}'";
if(isset($_POST['modalidad']))$filtro.=" and modalidad = '{$_POST['modalidad']}'";
if(isset($_POST['coord']))$filtro.=" and coordinacion = '{$_POST['coord']}'";
if(isset($_POST['nivel']))$filtro.=" and nivel = '{$_POST['nivel']}'";
$sql = "select p.id from ficha_programa as fp, programa as p where fp.id_programa = p.id $filtro group by p.id;";
$data = Conector::ejecutarQuery($sql);
$html = '<option value="">Selecciona un programa</option>';
foreach ($data as $obj) {
    $value=$obj['id'];
    $programa = new Programa('id',$value);
    $html .= '<option value="'.$value.'">'.$programa->getNombre().'</option>';
}
echo $html;