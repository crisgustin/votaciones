<?php
require_once '../clases/Conector.php';
require_once '../clases/NivelFormacion.php';
$filtro = '';
if(isset($_POST['oferta']))$filtro.=" and oferta = '{$_POST['oferta']}'";
if(isset($_POST['modalidad']))$filtro.=" and modalidad = '{$_POST['modalidad']}'";
if(isset($_POST['coord']))$filtro.=" and coordinacion = '{$_POST['coord']}'";
$sql = "select p.nivel from ficha_programa as fp, programa as p where fp.id_programa = p.id $filtro group by p.nivel;";
$data = Conector::ejecutarQuery($sql);
$html = '<option value="">Selecciona un nivel</option>';
foreach ($data as $obj) {
    $value=$obj['nivel'];
    $nivel = new NivelFormacion('id',$value);
    $html .= '<option value="'.$value.'">'.$nivel->getNombre().'</option>';
}
echo $html;