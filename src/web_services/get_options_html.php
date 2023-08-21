<?php
require_once '../clases/Conector.php';
require_once '../clases/Programa.php';
require_once '../clases/Coordinacion.php';
require_once '../clases/FichaPrograma.php';
$html = '';
if(isset($_POST['tipo'])) {
    switch ($_POST['tipo']) {
        case 'fichas':
            $filtro = "id_programa = {$_POST['id_programa']} and coordinacion = '{$_POST['coord']}'";
            $html .= FichaPrograma::getOptionsHTML($filtro,null);
            break;
        case 'programas':
            $filtro = "nivel = '{$_POST['nivel']}'";
            $html .= Programa::getOptionsHTML($filtro,null);
            break;
        case 'coords':
            $filtro = "id_programa = {$_POST['id_programa']}";
            $html .= Coordinacion::getOptionsHTML(null);break;
    }
} echo $html;