<?php
require_once '../clases/Conector.php';
require_once '../clases/Filter.php';
require_once '../clases/Encuesta.php';
require_once '../clases/Programa.php';
require_once '../clases/FichaPrograma.php';
require_once '../clases/NivelFormacion.php';

$html = $filtro = $subtitulo = $gestor = $correo = '';
if(isset($_POST['id_encuesta'])) {
    $encuesta = new Encuesta('id', $_POST['id_encuesta']);
    if(isset($_POST['id_programa'])||isset($_POST['id_ficha'])) {
        $filtro .= ' and ';
        if(isset($_POST['id_programa'])) {
            $filtro .= "fp.id_programa = {$_POST['id_programa']}";
            $programa = new Programa('id', $_POST['id_programa']);
            $subtitulo = $programa->getNombre_completo();
        } else {
            $filtro .= "fp.id = {$_POST['id_ficha']}";
            $ficha = new FichaPrograma('id', $_POST['id_ficha']);
            $subtitulo = $ficha->getNombre();
            $gestor = $ficha->getGestor();
            $correo = $ficha->getCorreo();
        }
    }
    $SQL = "select u.usuario, u.nombres, u.apellidos, u.telefono, u.correo, fp.ficha from usuario as u, ficha_usuario as fu, ficha_programa as fp, encuesta_usuario as eu where u.id = fu.id_usuario and fp.id = fu.id_ficha and eu.id_ficha = fu.id and eu.estado = 'P'$filtro;";
    $html .= '<table class="table table-bordered table-condensed">';
    $html .= '<thead><tr><th colspan="6">Incumplimiento en el diligenciar encuesta</th></tr>';
    $html .= '<tr><th colspan="6">'.$encuesta->getNombre().'</th></tr>';
    $html .= '<tr><th colspan="6">'.$subtitulo.'</th></tr>';
    if(isset($_POST['id_ficha'])) {
        $html .= '<tr><th>Gestor</th><td colspan="5">'.$gestor.'</td></tr>';
        $html .= '<tr><th>Correo Gestor</th><td colspan="5">'.$correo.'</td></tr>';
    }
    $html .= '<tr><th>Usuario</th><th>Nombres</th><th>Apellidos</th><th>Teléfono</th><th>Correo</th><th>Ficha</th></tr></thead>';
    $res = Conector::ejecutarQuery($SQL);
    $html .= '<tbody>';
    foreach($res as $obj) {
        $html .= '<tr>';
        foreach($obj as $key => $value) {
            if(is_numeric($key)) unset($key);
            else $html .= '<td>'.$value.'</td>';
        }$html .= '</tr>';
    }$html .= '</tbody>';
    $html .= '</table>';
    echo $html;
}