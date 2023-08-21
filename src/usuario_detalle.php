<?php

if(isset($_GET['id'])&&$usuario_logueado->getId_rol()==1) {

    

    $html = '<a onclick="ir_atras()" class="btn btn-sm btn-default active" title="Ir a la pagina anterior" data-placement="right"><span class="fa fa-chevron-left"></span>Atrás</a>';

    $html .= '<h3 class="text-bold text-center">Detalles de usuario</h3>';

    $objeto = new Usuario('usuarios.id', $_GET['id']);

    if($objeto->getUsuario()!=null) {

        $id_rol = $objeto->getId_rol();

        $html .= '<table border="1" class="table table-bordered table-condensed table-hover">

            <tr><th rowspan="2">Identificación</th><td>'.$objeto->getTipo_documento()->getNombre().'</td><th>Nombres</th><td>'.$objeto->getNombres().'</td></tr>

            <tr><td>'.$objeto->getDocumento().'</td><th>Apellidos</th><td>'.$objeto->getApellidos().'</td></tr>

            <tr><th>Correo electrónico</th><td>'.$objeto->getCorreo().'</td><th>Teléfono</th><td>'.$objeto->getTelefono().'</td></tr>

            <tr><th>Rol</th><td colspan="3" class="text-italic"><strong>'.strtoupper($objeto->getRol()->getNombre()).': </strong>'.$objeto->getRol()->getDescripcion().'</td></tr>

        </table>';

        $html .= '<a onclick="cargar_contenido_modal(this.href)" href="src/recuperar_clave.php?id_usuario='.$objeto->getId().'" class="btn btn-sm btn-warning" title="Recuperar contraseña" data-placement="top" data-toggle="modal" data-target="#modal"><span class="fa fa-envelope-o"></span> Recordar contraseña</a>';

        //$html .= '<a onclick="cargar_contenido_modal(this.href)" href="src/recuperar_clave.php?id_usuario='.$objeto->getId().'" class="btn btn-sm btn-warning" title="Mirar Hoja de Vida" data-placement="top" data-toggle="modal" data-target="#modal"><span class="fa fa-caret-down"></span> Hoja de Vida</a>';

        

        

    } else $html .= '<div class="alert alert-danger"><span class="fa fa-warning"></span>Usuario no encontrado</div>';

    echo $html;

} else header('Location: /bomberos');