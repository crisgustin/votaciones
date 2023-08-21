<?php
require_once 'clases/Conector.php';
require_once 'clases/Usuario.php';
require_once 'clases/Candidato.php';
require_once 'clases/Votacion.php';

date_default_timezone_set('America/Bogota');

foreach ($_POST as $key => $value) ${$key}=$value;
foreach ($_GET as $key => $value) ${$key}=$value;

$objeto = new Votacion(null,null);
$candidato = new Candidato('candidatos.idCandidato', $idCandidato);
$accion = 'Adicionar';
if(isset($_GET['idVotante'])) {

    $objeto = new Votacion('votacion.idVotante', $idVotante);
    $accion = 'Modificar';
} else if(count($_POST)>0) {

    $accion = $_POST['accion'];
    $objeto = new Votacion(null, null);
    //print_r($idVotante);
    $objeto->setIdVotante($idVotante);
    if($accion=='Modificar'){
        $resultado = $objeto->grabar();
        $votos=Conector::ejecutarQuery("select votos from candidatos where idCandidato='".$idCandidato."'");
        $suma=intval($votos[0][0]);
        $suma=$suma+1;
        Conector::ejecutarQuery("update candidatos set votos=".$suma." where IdCandidato='".$idCandidato."'");
    }
    if($resultado=='') $resultado = '<div class="alert alert-success text-center">¡Datos guardados con éxito!</div>';
    else $resultado = '<div class="alert alert-danger text-center">¡'.$resultado.'!</div>';
    $resultado.='<script type="text/javascript">setTimeout(function(){window.history.back()},10000)</script>';
    echo $resultado;die();
}
?>
<form name="usuario_form" method="POST" action="?contenido=src/frm_voto.php">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Votacion</h4>
    </div>
    <div class="modal-body">
        <h4>VOTO PARA <?=$candidato->getNombres_completos()?></h4>        
    </div>
    <div class="modal-footer">
        <div class="btn-group">
            <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span>Cancelar</button>
            <button type="submit" class="btn btn-warning"><span class="fa fa-save"></span>Guardar</button>
        </div>
    </div>
    <input type="hidden" id="idVotante" name="idVotante" value="<?=$idVotante?>"/>
    <input type="hidden" id="idCandidato" name="idCandidato" value="<?=$candidato->getIdCandidato()?>"/>
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