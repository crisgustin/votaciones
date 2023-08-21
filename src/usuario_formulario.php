<?php
require_once 'clases/Conector.php';
require_once 'clases/Usuario.php';
require_once 'clases/TipoDocumento.php';
require_once 'clases/Rol.php';
require_once 'clases/Dependencia.php';
$objeto = new Usuario(null,null);
$accion = 'Adicionar';
if(isset($_GET['id'])) {
    $objeto = new Usuario('usuarios.id', $_GET['id']);
    $accion = 'Modificar';
} else if(count($_POST)>0) {
    //print_r($_POST['telefono']);
    $accion = $_POST['accion'];
    $usuario = $_POST['tipo_documento'].$_POST['documento'];
    if($_POST['id']!='') $objeto = new Usuario('id', $_POST['id']);
    $objeto->setUsuario($usuario);
    $objeto->setNombres($_POST['nombres']);
    $objeto->setApellidos($_POST['apellidos']);
    $objeto->setId_rol($_POST['id_rol']);
    if($accion=='Adicionar'){
        $objeto->setClave($_POST['documento']);
        $resultado = $objeto->grabar();
    } else $resultado = $objeto->modificar();
    if($resultado=='') $resultado = '<div class="alert alert-success text-center">¡Datos guardados con éxito!</div>';
    else $resultado = '<div class="alert alert-danger text-center">¡'.$resultado.'!</div>';
    $resultado.='<script type="text/javascript">setTimeout(function(){window.history.back()},3000)</script>';
    echo $resultado;die();
}
?>
<form name="usuario_form" method="POST" action="?contenido=src/usuario_formulario.php">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Formulario de usuarios</h4>
    </div>
    <div class="modal-body">
        <span class="nota text-danger text-center"></span>
        <div class="row">
            <div class="form-group col col-md-6 col-sm-6 col-xs-12">
                <label for="tipo_documento">Tipo de identificación <span class="text-danger">*</span></label><br/>
                <select name="tipo_documento" id="tipo_documento" class="selectpicker form-control" required><?= TipoDocumento::getOptionsHTML($objeto->getTipo_documento()->getId())?></select>
            </div>
            <div class="form-group col col-md-6 col-sm-6 col-xs-12">
                <label for="documento"># Documento <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="documento" name="documento" value="<?=$objeto->getDocumento()?>" placeholder="Número de identificación" required>
            </div>
        </div>
        <div class="row">
            <div class="form-group col col-md-6 col-sm-6 col-xs-12">
                <label for="nombres">Nombres <span class="text-danger">*</span></label><br/>
                <input type="text" class="form-control" id="nombres" name="nombres" value="<?=$objeto->getNombres()?>" placeholder="Nombres personales" required/>
            </div>
            <div class="form-group col col-md-6 col-sm-6 col-xs-12">
                <label for="apellidos">Apellidos <span class="text-danger">*</span></label><br/>
                <input type="text" class="form-control" id="apellidos" name="apellidos" value="<?=$objeto->getApellidos()?>" placeholder="Apellidos personales" required/>
            </div>
        </div>
        
        <div class="row">
            <div class="form-group col col-md-12 col-sm-12 col-xs-12">
                <label for="id_rol">Rol de usuario <span class="text-danger">*</span></label><br/>
                <select name="id_rol" id="id_rol" class="selectpicker form-control" required><?=Rol::getOptionsHTML($objeto->getId_rol())?></select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <div class="btn-group">
            <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span>Cancelar</button>
            <button type="submit" class="btn btn-warning"><span class="fa fa-save"></span>Guardar</button>
        </div>
    </div>
    <input type="hidden" id="id" name="id" value="<?=$objeto->getId()?>"/>
    <input type="hidden" name="accion" value="<?=$accion?>"/>
</form>
<script type="text/javascript">
    $('.selectpicker').selectpicker()
    $('#documento').on('keydown',function(e){
        $('nota').addClass('hidden').text('');
        var chars=['1','2','3','4','5','6','7','8','9','0','Backspace','Tab'];
        if(!chars.includes(e.key))e.preventDefault()
    })
</script>