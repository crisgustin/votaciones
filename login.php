<?php 
require_once 'src/clases/Conector.php';
require_once 'src/clases/Usuario.php';
require_once 'src/clases/TipoDocumento.php';
if(count($_POST)>0) {
    $usuario = $_POST['tipo_documento'].$_POST['documento'];
    $respuesta = Usuario::validarIngreso($usuario,$_POST['clave']);
    echo $respuesta;
    die();
}
?>
<form id="login_form" method="POST" action="login.php">
    <div class="modal-header">
        <img class="modal-image" src="imagenes/del.png"/>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title text-center">Inicio de sesión</h4>
    </div>
    <div class="modal-body">
        <h5 class="nota text-danger text-center"></h5>
        <div class="form-group">
            <label for="tipo_documento">Tipo de identificación <span class="text-danger">*</span></label><br/>
            <select name="tipo_documento" id="tipo_documento" class="selectpicker form-control" required><?= TipoDocumento::getOptionsHTML(null)?></select>
        </div>
        <div class="form-group has-feedback">
            <label for="documento">Documento <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="documento" name="documento" placeholder="Número de identificación" required>
            <span class="fa fa-address-card form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            <label for="clave">Contraseña <span class="text-danger">*</span></label><br/>
            <input type="password" class="form-control" id="clave" name="clave" placeholder="**********" required/>
            <span class="fa fa-key form-control-feedback"></span>
        </div>
    </div>
    <div class="modal-footer">
        <a href="?contenido=src/identidad.php" class="btn btn-link btn-sm pull-left">Recuperar acceso</a>
        <div class="btn-group">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            <button type="submit" id="submit" class="btn btn-primary">Ingresar</button>
        </div>
    </div>
</form>
<link rel="stylesheet" type="text/css" href="lib/css/login.css"/>
<script type="text/javascript" src="lib/js/login.js"></script>