<?php
require_once 'clases/Conector.php';
require_once 'clases/Usuario.php';
require_once 'clases/Dependencia.php';
require_once 'clases/Localidad.php';
require_once 'clases/Emergencia.php';
require_once 'clases/EstadoEmergencia.php';
require_once 'clases/TipoEmergencia.php';
require_once 'clases/EventoPpal.php';
require_once 'clases/SubPpal.php';
require_once 'clases/Afectaciones.php';
require_once 'clases/Poblacion.php';

date_default_timezone_set('America/Bogota');



?>
<form name="usuario_form" method="POST" action="?contenido=src/reporte1.php">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h2 class="modal-title"><center>Formulario de Generación de Reportes</center></h2>
    </div>
    <div class="modal-body">
        <span class="nota text-danger text-center"></span>
        
        <div class="row">
            <div class="form-group col col-md-12 col-sm-12 col-xs-12">
                <label for="tinci">Tipo Incidente </label><br/>
                <select name="tinci" id="tinci" class="selectpicker form-control"><?=EventoPpal::getOptionsHTML(null)?></select>
            </div>
        </div>
        <div class="row">
            <div class="form-group col col-md-12 col-sm-12 col-xs-12">
                <label for="tipo_emergencia">Clasificación Incidente</label><br/>
                <select name="tipo_emergencia" id="tipo_emergencia" class="selectpicker form-control"><?=TipoEmergencia::getOptionsHTML(null)?></select>
            </div>
        </div>
        <div class="row">
            <div class="form-group col col-md-12 col-sm-12 col-xs-12">
                <label for="subtipo">Sub Clasificación Incidente</label><br/>
                <select name="subtipo" id="subtipo" class="selectpicker form-control"><?=SubPpal::getOptionsHTML(null)?></select>
            </div>
        </div>
        
        <h4 style="color:red">Localización</h4>
        <div class="row">
            <div class="form-group col col-md-6 col-sm-6 col-xs-12">
                <label for="localidad">Nombre Localidad / Barrio</label><br/>
                <select name="localidad" id="localidad" class="selectpicker form-control" data-live-search="true"><?=Localidad::getOptionsHTML(null)?></select>
            </div>
            <div class="form-group col col-md-6 col-sm-6 col-xs-12">
                <label for="poblacion">Tipo de Población</label><br/>
                <select name="poblacion" id="poblacion" class="selectpicker form-control"><?=Poblacion::getOptionsHTML(null)?></select>
            </div>
        </div>
        
        <div class="row">
            <div class="form-group col col-md-12 col-sm-12 col-xs-12">
                <label for="tipo_emergencia_madre">Tipo Evento Madre</label><br/>
                <select name="tipo_emergencia_madre" id="tipo_emergencia_madre" class="selectpicker form-control"><?=TipoEmergencia::getOptionsHTML2(null)?></select>
            </div>
        </div>
        <div class="row">
            <div class="form-group col col-md-6 col-sm-6 col-xs-12">
                <label for="fecini">Fecha Inicial Reporte</label></br>
                <input type="date" class="form-control" name="fecini" id="fecini"/>
            </div>
            <div class="form-group col col-md-6 col-sm-6 col-xs-12">
                <label for="fecfin">Fecha Final Reporte</label></br>
                <input type="date" class="form-control" name="fecfin" id="fecfin"/>
            </div>
        </div>
        <div class="row">
            <div class="form-group col col-md-12 col-sm-12 col-xs-12">
                <label for="estado">Estado del Incidente</label></br>
                <select name="estado" id="estado" class="selectpicker form-control"><?=EstadoEmergencia::getOptionsHTML(null)?></select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <div class="btn-group">
            <button type="submit" class="btn btn-warning"><span class="fa fa-save"></span>Generar Reporte</button>
        </div>
    </div>
</form>
<script type="text/javascript">
    $('.selectpicker').selectpicker()
    $('#documento').on('keydown',function(e){
        $('nota').addClass('hidden').text('');
        var chars=['1','2','3','4','5','6','7','8','9','0','Backspace','Tab'];
        if(!chars.includes(e.key))e.preventDefault()
    })

    $('#tinci').on('change', function(event){
        let value=event.target.value;
        let data=new FormData();
        data.append('tinci',value);
        fetch("src/web_services/optionsIncidente.php",{method:'POST',body:data}).then(response=>response.text()).then(data=>{$('#tipo_emergencia').html(data);$('#tipo_emergencia').selectpicker('refresh')});
    })

    $('#tipo_emergencia').on('change', function(event){
        let value=event.target.value;
        let data=new FormData();
        data.append('tipo_emergencia',value);
        fetch("src/web_services/optionsSubIncidente.php",{method:'POST',body:data}).then(response=>response.text()).then(data=>{$('#subtipo').html(data);$('#subtipo').selectpicker('refresh')});
    })




</script>

