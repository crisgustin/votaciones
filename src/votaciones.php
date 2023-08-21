<?php
require_once 'clases/Candidato.php';
$votos=Conector::ejecutarQuery("select * from votacion where idVotante='".$usuario_logueado->getId()."'");
if ($usuario_logueado->getId_rol()==2 and $votos==null) {
    $filtro = '';
    $rol = $texto = null;
    $html ='<br/><br/><br/>';
    $html .= '<h3 class="text-center">RESULTADOS ELECCIONES</h3>';
    foreach ($_GET as $key => $value) ${$key} = $value;
    $count = Candidato::getCount();
    if ($count > 0) {
        if ($filtro == null) $filtro .= 'idCandidato is not null ';
        $datos = Candidato::getListaEnObjetos($filtro);
        $html .= '<table border="1" class="table table-bordered table-collapsed table-hover">';
        $html .= '<thead><tr class="active">';
        foreach($datos as $imagenes){
            $html .= '<th><center><img src="'.$imagenes->getFoto().'" width="150px" height="180"/></center></th>';
        }
        $html .= '</tr></thead>';
        $html .= '<tr>';
        foreach ($datos as $usuario) {
            $nombres = trim($usuario->getNombres() . ' ' . $usuario->getApellidos());
            $nombres = strtoupper($nombres);
            $html .= '<td data-label="Nombres">' . $nombres . '</td>';
        }
        $html .= '</tr><tr>';
        foreach ($datos as $usuario) {
            $html .= '<td><center><a onclick="cargar_contenido_modal(this.href)" class="btn-table" data-toggle="modal" data-target="#modal" href="src/frm_voto.php?idCandidato=' . $usuario->getIdCandidato() . '&idVotante='.$usuario_logueado->getId().'" class="btn-table" title="Formulario de Votación" >VOTAR <span class="fa fa-edit"></span></a></td>';
        }
        $html .= '</tr>';
        
        $html .= '</table>';
        $html .= '<script type="text/javascript" src="lib/js/filtro.js"></script>';
    } else {
        $html .= '<h4 class="text-center">¡Ningún Candidato Registrado!</h4>';
        $html .= '<div class="col col-md-6 col-sm-6 col-xs-6 text-right">'
            . '<div class="btn-group"><a onclick="cargar_contenido_modal(this.href)" href="src/candidato_formulario.php" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modal" title="Adicionar Nuevo Candidato" data-placement="top"><span class="fa fa-plus"></span> Nuevo Candidato</a>'
            . '</div></div>';
    }
} else header('Location: /votaciones');
echo $html;
?>