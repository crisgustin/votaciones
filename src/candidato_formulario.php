<?php
require_once 'clases/Conector.php';
require_once 'clases/Usuario.php';
require_once 'clases/Candidato.php';
$objeto = new Candidato(null,null);
$accion = 'Adicionar';
if(isset($_GET['idCandidato'])) {
    $objeto = new Candidato('candidatos.idCandidato', $_GET['idCandidato']);
    $accion = 'Modificar';
} else if(count($_POST)>0) {
    $accion = $_POST['accion'];
    if($_POST['idCandidato']!='') $objeto = new Candidato('idCandidato', $_POST['idCandidato']);
    $objeto->setIdCandidato($_POST['documento']);
    $objeto->setNombres($_POST['nombres']);
    $objeto->setApellidos($_POST['apellidos']);
    if($accion=='Adicionar'){
        echo "<div>";
            $directorio = 'src/imagenes/';
            $subir_archivo = $directorio.($_FILES['subir_archivo']['name']);
            $subir_archivo =str_replace(' ', '_', $subir_archivo);
            if (move_uploaded_file($_FILES['subir_archivo']['tmp_name'], $subir_archivo)) {
                echo "El archivo es válido y se cargó correctamente.<br><br>";
                echo"<a href='".$subir_archivo."' target='_blank'><img src='".$subir_archivo."' width='150'></a>";
                $objeto->setFoto($subir_archivo);
                
                }
                
            else {
                echo "La subida ha fallado";
            }

            
            echo "</div>";
        $resultado = $objeto->grabar();
    } else $resultado = $objeto->modificar();
    if($resultado=='') $resultado = '<div class="alert alert-success text-center">¡Datos guardados con éxito!</div>';
    else $resultado = '<div class="alert alert-danger text-center">¡'.$resultado.'!</div>';
    $resultado.='<script type="text/javascript">setTimeout(function(){window.history.back()},1000)</script>';
    echo $resultado;die();
}
?>
<form enctype="multipart/form-data" name="usuario_form" method="POST" action="?contenido=src/candidato_formulario.php">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Formulario de Candidatos</h4>
    </div>
    <div class="modal-body">
        <span class="nota text-danger text-center"></span>
        <div class="row">
            <div class="form-group col col-md-12 col-sm-12 col-xs-12">
                <label for="documento"># Documento <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="documento" name="documento" value="<?=$objeto->getIdCandidato()?>" placeholder="Número de identificación" required>
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
                <label for="cargararchivo">Cargar Archivo <span class="text-danger"></span></label></br>
                <input name="subir_archivo" type="file" />
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <div class="btn-group">
            <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span>Cancelar</button>
            <button type="submit" class="btn btn-warning"><span class="fa fa-save"></span>Guardar</button>
        </div>
    </div>
    <input type="hidden" id="idCandidato" name="idCandidato" value="<?=$objeto->getIdCandidato()?>"/>
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

