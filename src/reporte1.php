<?php
//============================================================+
// File name   : example_001.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 001 for TCPDF class
//               Default Header and Footer
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Default Header and Footer
 * @author Nicola Asuni
 * @since 2008-03-04
 */

// Include the main TCPDF library (search for installation path).
date_default_timezone_set('America/Bogota');
$fecha= date('Y-m-d H:i:s');
require_once dirname(__FILE__).'/../vendor/autoload.php';
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
define('K_PATH_IMAGES',dirname(__FILE__).'/../imagenes/');
define('PDF_HEADER_LOGO','logo.png');
 define('PDF_HEADER_LOGO_WIDTH',18);
 $sqlAux='';

 if($_POST['tinci']!=null){
    $sqlAux .= " categoriaEmergencia=".$_POST['tinci'];
 }

 if($_POST['tipo_emergencia']!=null){
    if($sqlAux!=''){
        $sqlAux .= " and tipoEmergencia=".$_POST['tipo_emergencia'];
    }else{
        $sqlAux .= " tipoEmergencia=".$_POST['tipo_emergencia'];
    }
 }

 //print_r($_POST['subtipo']);

 if($_POST['subtipo']!=null){
    if($sqlAux!=''){
        $sqlAux .= " and subTipoEmergencia=".$_POST['subtipo'];
    }else{
        $sqlAux .= " subTipoEmergencia=".$_POST['subtipo'];
    }
 }

 if($_POST['localidad']!=null){
    if($sqlAux!=''){
        $sqlAux .= " and localidad=".$_POST['localidad'];
    }else{
        $sqlAux .= " localidad=".$_POST['localidad'];
    }
 }

 if($_POST['poblacion']!=null){
    if($sqlAux!=''){
        $sqlAux .= " and poblacion=".$_POST['poblacion'];
    }else{
        $sqlAux .= " poblacion=".$_POST['poblacion'];
    }
 }

 if($_POST['tipo_emergencia_madre']!=null){
    if($sqlAux!=''){
        $sqlAux .= " and tipoEventoMadre=".$_POST['tipo_emergencia_madre'];
    }else{
        $sqlAux .= " tipoEventoMadre=".$_POST['tipo_emergencia_madre'];
    }
 }

 if($_POST['estado']!=null){
    if($sqlAux!=''){
        $sqlAux .= " and estadoEmergencia=".$_POST['estado'];
    }else{
        $sqlAux .= " estadoEmergencia=".$_POST['estado'];
    }
 }

 if($_POST['fecini']!=null){
    if($_POST['fecfin']!=null){
        if($sqlAux!=''){
            $sqlAux .= " and horaReporte between '".$_POST['fecini']."' and '".$_POST['fecfin']."'";
        }else{
            $sqlAux .= " horaReporte between '".$_POST['fecini']."' and '".$_POST['fecfin']."'";
        }
    }else{
        if($sqlAux!=''){
            $sqlAux .= " and horaReporte between '".$_POST['fecini']."' and '".$fecha."'";
        }else{
            $sqlAux .= " horaReporte between '".$_POST['fecini']."' and '".$fecha."'";
        }
    }
 }

 if($sqlAux!=''){
    $sqlAux .= ' order by idEmergencia desc';
}else{
    $sqlAux .= ' idEmergencia>1 order by idEmergencia desc';
}

 
 

 if($sqlAux!=''){
    $datos = Emergencia::getListaEnObjetos($sqlAux);
 }else{
    $datos = Emergencia::getListaEnObjetos(null);
 }

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 001');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data

$titulo='Reporte de Incidentes';
$subtitulo='SIRE 119 - Sistema Integrado de Respuesta a Emergencias';

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $titulo, $subtitulo, array(0,0,0), array(0,64,0));
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,0,255), array(0,64,128));
$pdf->setFooterData(array(0,0,0), array(0,64,128));

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('freesans', '', 6, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

// set text shadow effect
$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

// Set some content to print
//$html = <<<EOD
  //  <h4>Hola mundo</h4>
    //EOD;

$html = '<table border="1" class="table table-bordered table-collapsed table-hover">';
$html .= '<thead><tr class="active"><th>Id</th><th>Dirección del Incidente</th><th>Categoria Incidente</th><th>Tipo Incidente</th><th>Sub Tipo Incidente</th><th>Hora Reporte</th><th>Hora Cierre</th><th>Estado</th></tr></thead>';
foreach ($datos as $usuario) {
            //$html .= '<div class="row"><div class="col col-md-12 col-xs-8">';
            $html .= '<tr>';
            $html .= '<td>' . $usuario->getIdEmergencia() . '</td>';
            $html .= '<td>' . $usuario->getDireccionEmergencia() .' - '.$usuario->getPRef(). '</td>';
            $categoria=Conector::ejecutarQuery("select nombreEvento from eventosppales where id=".$usuario->getCategoriaEmergencia());
            $html .= '<td>' . $categoria[0][0] . '</td>';
            $tipoemergencia = Conector::ejecutarQuery("select tipoEmergencia from tipoemergencia where idTipoEmergencia=".$usuario->getTipoEmergencia());
            $html .= '<td>' . $tipoemergencia[0][0] . '</td>';
            $subtipo=Conector::ejecutarQuery("select nombreSub from subppales where id=".$usuario->getSubTipoEmergencia());
            $html .= '<td>' . $subtipo[0][0] . '</td>';
            $html .= '<td>' . $usuario->getHoraReporte() . '</td>';
            $html .= '<td>' . $usuario->getHoraCierre() . '</td>';
            $estadoEmer=Conector::ejecutarQuery("select estadoEmergencia from estadoemergencia where idEstadoEmergencia=".$usuario->getEstadoEmergencia());
            if($usuario->getEstadoEmergencia()==1){
                $html .= '<td bgcolor="red">' . $estadoEmer[0][0] . '</td>';
            }else if($usuario->getEstadoEmergencia()==2){
                $html .= '<td bgcolor="yellow">' . $estadoEmer[0][0] . '</td>';
            }else if($usuario->getEstadoEmergencia()==3)
            {
                $html .= '<td bgcolor="green">' . $estadoEmer[0][0] . '</td>';
            }else{
                $html .= '<td bgcolor="#5564eb">' . $estadoEmer[0][0] . '</td>';
            }
            
            
            $html .= '</tr>';
            //$html .= '</div>';
        }
        $html .= '</table>';
        print_r($html);
        //die();

//Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
ob_clean();
$pdf->Output('reporte.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
