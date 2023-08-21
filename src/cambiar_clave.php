<?php
if(count($_POST)>0) {
    require_once dirname(__FILE__).'/clases/Conector.php';
    require_once dirname(__FILE__).'/clases/Usuario.php';
    @session_start();
    $usuario = unserialize ($_SESSION['usuario']);
    $usuario->setClave($_POST['clave']);
    $resultado = $usuario->modificar();
    print_r($resultado);die();
}?>
<form id="form" method="POST" action="?contenido=src/cambiar_clave.php">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Cambio de contraseña de acceso</h4>
    </div>
    <div class="modal-body">
        <span class="text-danger text-bold nota"></span>
        <div class="form-group">
            <label for="clave">Nueva contraseña <span class="text-danger">*</span></label>
            <input type="password" id="clave" name="clave" class="form-control" placeholder="*********" required/>
        </div>
        <div class="form-group">
            <label for="clave">Confirmación de contraseña <span class="text-danger">*</span></label>
            <input type="password" id="confirm" class="form-control" placeholder="*********" required/>
        </div>
    </div>
    <div class="modal-footer">
        <div class="btn-group">
            <button type="button" class="btn btn-warning" data-dismiss="modal"><span class="fa fa-close"></span>Cancelar</button>
            <button type="submit" class="btn btn-warning"><span class="fa fa-save"></span>Guardar</button>
        </div>
    </div>
</form>
<script type="text/javascript">
    $('#form').on('submit',function(e){
        var nueva = $('#clave').val();
        var confirm = $('#confirm').val();
        if(nueva!=confirm) {
            $('.nota').text('¡Las contraseñas no coinciden!');
            $('.form-group').addClass('has-error');
        } else {
            $.ajax({
                url:'src/cambiar_clave.php',type:'POST',
                data:{'clave':nueva},
                success:function(data) {
                    if(data=='') {
                        $('.nota').text('¡Cambio de contraseña exitoso!');
                        $('.nota').removeClass('text-danger').addClass('text-success');
                        setTimeout(function(){$('#modal').modal('toggle')},3000)
                    } else {
                        $('.nota').text('Hubo un error... Intentelo bnuevamente!');
                        $('.nota').removeClass('text-success').addClass('text-danger');
                    }
                }
            })
        }
        e.preventDefault()
    }),$('.form-control').on('keyup',function(){
        $('.form-group').removeClass('has-error');
        $('.nota').text('')
    })
</script>