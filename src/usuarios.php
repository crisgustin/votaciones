<?php
if ($usuario_logueado->getId_rol()==1) {
    $filtro = '';
    $rol = $texto = null;
    $html ='<br/><br/><br/>';
    $html .= '<h3 class="text-center">Lista de Usuarios</h3>';
    foreach ($_GET as $key => $value) ${$key} = $value;
    $count = Usuario::getCount();
    if ($count > 0) {
        require_once 'src/clases/Paginacion.php';
        $paginacion = new Paginacion($count, 50, $_GET['contenido']);
        $pagina = isset($_GET['pag']) ? $_GET['pag'] : 1;
        if ($rol != null) $filtro = "id_rol = $rol";
        if ($texto != null) {
            if ($filtro != null) $filtro .= ' and ';
            $filtro .= "usuario like '%$texto%' or nombre like '%$texto%' or apellidos like '%$texto%' or id_rol like '%$texto%'";
        }
        if ($filtro == null) $filtro .= 'usuario is not null ';
        $filtro .= "limit {$paginacion->rxp} offset {$paginacion->registro_inicial($pagina)}";
        $datos = Usuario::getListaEnObjetos($filtro);
        $html .= '<div class="row"><div class="col col-md-6 col-sm-6 col-xs-6">' . $paginacion->getEncabezado($pagina) . '</div>';
        $html .= '<div class="col col-md-6 col-sm-6 col-xs-6 text-right">'
            . '<div class="btn-group"><a onclick="cargar_contenido_modal(this.href)" href="src/usuario_formulario.php" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modal" title="Adicionar nuevo usuario" data-placement="top"><span class="fa fa-plus"></span> Nuevo</a>'
            . '</div></div></div>';
        $html .= '<table border="1" class="table table-bordered table-collapsed table-hover">';
        $html .= '<thead><tr class="active"><th>Identificación</th><th>Nombres</th><th>Rol</th><th>Gestión</th></tr></thead>';
        foreach ($datos as $usuario) {
            $nombres = trim($usuario->getNombres() . ' ' . $usuario->getApellidos());
            $nombres = strtoupper($nombres);
            $html .= '<tr>';
            $html .= '<td data-label="Identificación">' . $usuario->getTipo_documento()->getId() . ' - ' . $usuario->getDocumento() . '</td>';
            $html .= '<td data-label="Nombres">' . $nombres . '</td>';
            $html .= '<td data-label="Rol">' . $usuario->getRol()->getNombre() . '</td>';
            $html .= '<td class="text-center" data-label="Gestión">';
            $html .= '<a onclick="cargar_contenido_modal(this.href)" class="btn-table" data-toggle="modal" data-target="#modal" href="src/usuario_formulario.php?id=' . $usuario->getId() . '" title="Actualizar datos"><span class="fa fa-edit"></span></a>';
            $html .= '<a href="?contenido=src/usuario_detalle.php&id=' . $usuario->getId() . '" class="btn-table" title="Ver más detalles"><span class="fa fa-eye"></span></a>';
            $html .= '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>' . $paginacion->getPaginasHTML($pagina);
        $html .= '<script type="text/javascript" src="lib/js/filtro.js"></script>';
    } else $html .= '<h4 class="text-center">¡Ningún usuario registrado!</h4>';
} else header('Location: /bomberos');
echo $html;
?>