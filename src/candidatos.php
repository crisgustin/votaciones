<?php
require_once 'clases/Candidato.php';
if ($usuario_logueado->getId_rol()==1) {
    $filtro = '';
    $rol = $texto = null;
    $html ='<br/><br/><br/>';
    $html .= '<h3 class="text-center">Lista de Candidatos</h3>';
    foreach ($_GET as $key => $value) ${$key} = $value;
    $count = Candidato::getCount();
    if ($count > 0) {
        require_once 'src/clases/Paginacion.php';
        $paginacion = new Paginacion($count, 50, $_GET['contenido']);
        $pagina = isset($_GET['pag']) ? $_GET['pag'] : 1;
        if ($rol != null) $filtro = "id_rol = $rol";
        if ($texto != null) {
            if ($filtro != null) $filtro .= ' and ';
            $filtro .= "usuario like '%$texto%' or nombre like '%$texto%' or apellidos like '%$texto%' or id_rol like '%$texto%'";
        }
        if ($filtro == null) $filtro .= 'idCandidato is not null ';
        $filtro .= "limit {$paginacion->rxp} offset {$paginacion->registro_inicial($pagina)}";
        $datos = Candidato::getListaEnObjetos($filtro);
        $html .= '<div class="row"><div class="col col-md-6 col-sm-6 col-xs-6">' . $paginacion->getEncabezado($pagina) . '</div>';
        $html .= '<div class="col col-md-6 col-sm-6 col-xs-6 text-right">'
            . '<div class="btn-group"><a onclick="cargar_contenido_modal(this.href)" href="src/candidato_formulario.php" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modal" title="Adicionar Nuevo Candidato" data-placement="top"><span class="fa fa-plus"></span> Nuevo Candidato</a>'
            . '</div></div></div>';
        $html .= '<table border="1" class="table table-bordered table-collapsed table-hover">';
        $html .= '<thead><tr class="active"><th>Identificación</th><th>Nombres</th><th>Foto</th><th>Gestión</th></tr></thead>';
        foreach ($datos as $usuario) {
            $nombres = trim($usuario->getNombres() . ' ' . $usuario->getApellidos());
            $nombres = strtoupper($nombres);
            $html .= '<tr>';
            $html .= '<td data-label="Identificación">' . $usuario->getIdCandidato() . '</td>';
            $html .= '<td data-label="Nombres">' . $nombres . '</td>';
            $html .= '<td><center><img src="'.$usuario->getFoto().'" width="150px" height="180"/></center></td>';
            $html .= '<td class="text-center" data-label="Gestión">';
            $html .= '<a onclick="cargar_contenido_modal(this.href)" class="btn-table" data-toggle="modal" data-target="#modal" href="src/candidato_formulario.php?idCandidato=' . $usuario->getIdCandidato() . '" title="Actualizar datos"><span class="fa fa-edit"></span></a>';
            $html .= '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>' . $paginacion->getPaginasHTML($pagina);
        $html .= '<script type="text/javascript" src="lib/js/filtro.js"></script>';
    } else {
        $html .= '<h4 class="text-center">¡Ningún Candidato Registrado!</h4>';
        $html .= '<div class="col col-md-6 col-sm-6 col-xs-6 text-right">'
            . '<div class="btn-group"><a onclick="cargar_contenido_modal(this.href)" href="src/candidato_formulario.php" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modal" title="Adicionar Nuevo Candidato" data-placement="top"><span class="fa fa-plus"></span> Nuevo Candidato</a>'
            . '</div></div>';
    }
} else header('Location: /bomberos');
echo $html;
?>