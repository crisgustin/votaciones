<?php

require_once '../../clases/Conector.php';
require_once '../../clases/Encuesta.php';
require '../../../vendor/autoload.php';
    define('K_PATH_IMAGES','../../../imagenes/');
    define('PDF_HEADER_LOGO','sena_pdf.jpg');
    define('PDF_HEADER_LOGO_WIDTH',18);
    $titulo = "Resultados de encuesta aplicada a aprendices";
    $subtitulo = "Centro Internacional de Producción Limpia - Lope\nGestión de Formación Profesional Integral";
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION,PDF_UNIT,PDF_PAGE_FORMAT,true,'UTF-8');
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Encuestas SENA');
    $pdf->SetTitle('Indicadores de Encuestas');
    $pdf->SetSubject('Resultados por pregunta');
    $pdf->SetKeywords('SENA, Encuestas, Gráficas, Resultados, Conteo');
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $titulo, $subtitulo, array(0,0,0), array(0,64,0));
    $pdf->setFooterData(array(0,64,0), array(0,64,128));
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->setFontSubsetting(true);
    $pdf->SetFont('arial', '', 10, '', true);
    $pdf->AddPage();
    $html = file_get_contents('../../../export.html');
    $pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
    $pdf->writeHTML($html,false,false,false,false,'justify');
    $filename = 'reporte_'.date('YmdHis').'.pdf';
    ob_clean();
    $pdf->Output($filename, 'I');

