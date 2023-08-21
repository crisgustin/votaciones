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
require_once 'clases/Reporte.php';
define('K_PATH_IMAGES',dirname(__FILE__).'/../imagenes/');
define('PDF_HEADER_LOGO','logo.png');
 define('PDF_HEADER_LOGO_WIDTH',18);

 $objeto = new Emergencia('emergencias.idEmergencia', $_GET['idEmergencia']);
 

$titulo="Reporte Incidente No ".$objeto->getIdEmergencia();
$html .= '<h3 class="text-bold text-center">'.$titulo.'</h3>';
$hora=date('Y-m-d H:i:s');
$boletin=Conector::ejecutarQuery("select contador from boletines where idEmergencia=".$_GET['idEmergencia']);
$html .= '<h3 class="text-bold text-center">Boletin No '.$boletin[0][0].' - Fecha y Hora: '.$hora.'</h3>';
$html .= '<table border="1" class="table table-bordered table-collapsed table-hover">';
$html .= '<thead><tr class="active"><th>Id Emergencia</th><th>Teléfono de Reporte</th><th>Nombre de Reportante</th><th>Dirección de Emergencia</th><th>Tipo Emergencia</th><th>Hora Reporte</th><th>Hora Cierre</th><th>Estado</th></tr></thead>';

if($objeto->getIdEmergencia()!=null) {
    $html.='<tbody>';
    $html .= '<tr bordercolor="black">';
    $html .= '<td>' . $objeto->getIdEmergencia() . '</td>';
    $html .= '<td>' . $objeto->getTelefonoReporte() . '</td>';
    $html .= '<td>' . $objeto->getNombreReportante() . '</td>';
    $html .= '<td>' . $objeto->getDireccionEmergencia() .' - '.$objeto->getPRef(). '</td>';
    $tipoemergencia = Conector::ejecutarQuery("select tipoEmergencia from tipoemergencia where idTipoEmergencia=".$objeto->getTipoEmergencia());
    $html .= '<td>' . $tipoemergencia[0][0] . '</td>';
    $html .= '<td>' . $objeto->getHoraReporte() . '</td>';
    $html .= '<td>' . $objeto->getHoraCierre() . '</td>';
    $estadoEmer=Conector::ejecutarQuery("select estadoEmergencia from estadoemergencia where idEstadoEmergencia=".$objeto->getEstadoEmergencia());
    $html .= '<td>' . $estadoEmer[0][0] . '</td>';
    $html .= '</tr></tbody></table>';
    $filtroAfectaciones="idEmergencia=".$objeto->getIdEmergencia()." order by id asc ";
    $obj2=Afectaciones::getListaEnObjetos($filtroAfectaciones);
    $html .= '<h3><center> REPORTES DE AFECTACIONES </center></h3>';
    $html .= '<table border="1" class="table table-bordered table-collapsed table-hover">';
    $html .= '<thead><tr class="active"><th># Reporte</th>';
    if($objeto->getCategoriaEmergencia()!=2 && $objeto->getCategoriaEmergencia()!=3 && $objeto->getCategoriaEmergencia()!=6 && $objeto->getCategoriaEmergencia()!=7){
        $html .= '<th>Viviendas</th><th>Familias</th>';
    }
    $html .= '<th>Personas</th><th>Heridos</th><th>Fallecidos</th><th>Desaparecidos</th>';
    if($objeto->getCategoriaEmergencia()!=2 && $objeto->getCategoriaEmergencia()!=3 && $objeto->getCategoriaEmergencia()!=6 && $objeto->getCategoriaEmergencia()!=7){
        $html .= '<th>Área</th>';
    }
    if($objeto->getCategoriaEmergencia()==3){
        $html .='<th>Alcoholizados</th>';
    }
    if($objeto->getCategoriaEmergencia()==1 || ($objeto->getCategoriaEmergencia()==2 && $objeto->getTipoEmergencia()==38)){
        $html .= '<th>Animales</th>';
    }
    if(($objeto->getCategoriaEmergencia()==1 || $objeto->getCategoriaEmergencia()==5) && $objeto->getRegion()==2){
        $html .= '<th>Cultivos</th>';
        $html .= '<th>Observaciones</th>';
        }
    $html .= '<th>Hora</th></tr></thead>';
    $html.='<tbody>';
    $cont=1;
    foreach($obj2 as $objeto2){
        $html .= '<tr>';
        $html .= '<td>' . $cont . '</td>';
        if($objeto->getCategoriaEmergencia()!=2 && $objeto->getCategoriaEmergencia()!=3 && $objeto->getCategoriaEmergencia()!=6 && $objeto->getCategoriaEmergencia()!=7){
            $html .= '<td>' . $objeto2->getCasasAfectadas() . '</td>';
            $html .= '<td>' . $objeto2->getFamiliasAfectadas() . '</td>';
        }
        $html .= '<td>' . $objeto2->getPersonasAfectadas() . '</td>';
        $html .= '<td>' . $objeto2->getHeridos() . '</td>';
        $html .= '<td>' . $objeto2->getFallecidos() . '</td>';
        $html .= '<td>' . $objeto2->getDesaparecidos() . '</td>';
        if($objeto->getCategoriaEmergencia()!=2 && $objeto->getCategoriaEmergencia()!=3 && $objeto->getCategoriaEmergencia()!=6 && $objeto->getCategoriaEmergencia()!=7){
            $html .= '<td>' . $objeto2->getAreaAfectada() . '</td>';
        }
        if($objeto->getCategoriaEmergencia()==3){
            $html .= '<td>' . $objeto2->getAlcoholizados() . '</td>';
        }
        if($objeto->getCategoriaEmergencia()==1 || ($objeto->getCategoriaEmergencia()==2 && $objeto->getTipoEmergencia()==38)){
            $html .= '<td>' . $objeto2->getAnimales() . '</td>';
        }
        if(($objeto->getCategoriaEmergencia()==1 || $objeto->getCategoriaEmergencia()==5) && $objeto->getRegion()==2){
            $html .= '<td>' . $objeto2->getCultivosAfectados() . '</td>';
            $html .= '<td>' . $objeto2->getDesCultivos() . '</td>';
        }
        $html .= '<td>' . $objeto2->getHora() . '</td>';
        $html .= '</tr>';
        $cont=$cont+1;
    }
    $html .= '</tbody></table>';
    /*$descripcion=$objeto->getDescripcionEmergencia();
    $order   = array("\n", "\r");
    $replace = '<br />';
    $newstr = str_replace($order, $replace, $descripcion);*/

    $html .= '<center><h4>DESCRIPCION DE LA EMERGENCIA</h4></center>';
    $filtroReporte = "idEmergencia=".$objeto->getIdEmergencia()." order by id asc ";
    $objR = Reporte::getListaEnObjetos($filtroReporte);
    $html .= '<table border="1" class="table table-bordered table-collapsed table-hover">';
    $html .= '<thead><tr class="active"><th>Entidad Reportante</th><th>Hora Reporte</th><th>Descripción</th><th>Usuario Reportante</th></tr></thead>';
    $html.='<tbody>';
    foreach($objR as $objetoReporte){
        $html .= '<tr>';
        $html .= '<td>' . $objetoReporte->getDependencia() . '</td>';
        $html .= '<td>' . $objetoReporte->getHora() . '</td>';
        $html .= '<td>' . $objetoReporte->getDescripcion() . '</td>';
        $usuarioReportante=Conector::ejecutarQuery("select nombres, apellidos from usuarios where id=".$objetoReporte->getUsuarioReportante());
        $html .= '<td>' . $usuarioReportante[0][0].' '.$usuarioReportante[0][1] . '</td>';
        $html .= '</tr>';
    }
    $html .= '</tbody></table>';

    /*$html .= '<textarea>';
    $html .= '<h3 class="text-bold text-center">DESCRIPCION DE ACTIVIDADES REALIZADAS</h3>';
    $html .= $newstr;

    $html .= '</textarea>';*/
    $html .= '<br></br><br></br><br></br>';
    $html .= '<h3>__________________________________</h3>';
    $html .= '<h3>'. $_GET['nombrescompletos'].'</h3>';
    $html .= '<h3>Jefe de Sala COE - SIRE119</h3>';
    $cont=intval($boletin[0][0]);
    $cont=$cont+1;
    Conector::ejecutarQuery("update boletines set contador=".$cont." where idEmergencia=".$_GET['idEmergencia']);
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

//$titulo='Reporte de Incidentes';
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


//Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
ob_clean();

$nombre='Boletin No '.$boletin[0][0].'_Incidente'.$_GET['idEmergencia'].'.pdf';
$pdf->Output($nombre, 'I');

//============================================================+
// END OF FILE
//============================================================+
