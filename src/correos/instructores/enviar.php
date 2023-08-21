<?php
date_default_timezone_set('America/Bogota');
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use \PhpOffice\PhpSpreadsheet\Reader\Html;
require_once dirname(__FILE__).'/../../clases/Conector.php';
require_once dirname(__FILE__).'/../../clases/Mailer.php';
require_once dirname(__FILE__).'/../../clases/NivelFormacion.php';
require_once dirname(__FILE__).'/../../../vendor/autoload.php';
$sql = "select fp.ficha, concat(p.nivel,' en ',p.nombre) as nombre_programa, fp.gestor, fp.correo from encuesta_usuario as eu, ficha_usuario as fu, ficha_programa as fp, programa as p where eu.id_ficha = fu.id and eu.estado = 'P' and fu.id_ficha = fp.id and fp.id_programa = p.id group by fp.id;";
$datos = Conector::ejecutarQuery($sql);
if(count($datos)>0) {
    $excel_html = '<table>';
    $excel_html .= '<thead><tr nobr="true"><th colspan="4">Programas de formación con encuesta asignada</th></tr><tr><th colspan="4">Encuesta de satisfacción proceso de formación profesional integral</th></tr><tr><th>Ficha</th><th>Programa de formación</th><th>Gestor</th><th>Correo Gestor</th></tr></thead>';
    $excel_html .= '<tbody>';
	$mail = new Mailer();
    $correos=[];$i=0;
    foreach($datos as $objeto) {
        $nivel = substr($objeto['nombre_programa'],0,2);
        $nivel = new NivelFormacion('id',$nivel);
        $programa = $nivel->getNombre().' '.substr($objeto['nombre_programa'],3);
        $excel_html .= '<tr>';
        $excel_html .= '<td>'.$objeto['ficha'].'</td>';
        $excel_html .= '<td>'.$programa.'</td>';
        $excel_html .= '<td>'.$objeto['gestor'].'</td>';
        $excel_html .= '<td>'.$objeto['correo'].'</td>';
        $excel_html .= '</tr>';
        if(!in_array($objeto['correo'],$correos)){
            $mail->addEmail($objeto['correo']);
            $i++;
        }
        array_push($correos,$objeto['correo']);
    }
    echo count($datos).' - '.$i;
    $excel_html .= '<tbody>';
    $excel_html .= '</table>';
    $reader = new Html();
    $spreadsheet = $reader->loadFromString($excel_html);
    $properties = $spreadsheet->getProperties();
    $properties->setCreator('SI de Encuestas');
    $properties->setCompany('SENA Regional Nariño');
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Hoja 1');
    $count_rows = $sheet->getHighestRow();
    $cols = ['A','B','C','D'];
    $end_col = $sheet->getHighestColumn();
    $index_end_col = array_search($end_col,$cols);
    $i = 0;
    while($i<=$index_end_col) {
        $sheet->getColumnDimension($cols[$i])->setAutoSize(true);
        $i++;
    }
    $header_row = 3;
    $sheet->getRowDimension(1)->setRowHeight(40);
    $style_sheet = ['font' => ['name' => 'Arial', 'size' => 10],'alignment' => ['wrapText' => true,'vertical' => Alignment::VERTICAL_CENTER], 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM,'color' => ['argb' => '000000']]]];
    $style_head = ['font' => ['bold' => true], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]];
    $sheet->getStyle("A1:$end_col$count_rows")->applyFromArray($style_sheet);
    $sheet->getStyle("A1:$end_col$header_row")->applyFromArray($style_head);
    $font_title = $sheet->getStyle("A1")->getFont();
    $font_title->setSize(20);
    $font_subtitle = $sheet->getStyle("A2")->getFont();
    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $filename = '../reporte_'.date('Y-m-d_His').'.xlsx';
    $writer->save($filename);
    $mail->setAsunto('Encuesta de satisfacción proceso de formación profesional integral');
    $html = '<p>Cordial saludo</p><p>Estimado Instructor</p><br><p style="text-align:justify">Por medio de este correo el Grupo de Gestión de Formación Profesional Integral del Centro Internacional de Producción Limpia -  Lope le informa que a los aprendices que ya están a punto de culminar su etapa lectiva se les ha asignado la tarea de responder la siguiente encuesta: <strong>Encuesta de satisfacción proceso de formación profesional integral</strong>, que tiene como objetivo principal conocer las opiniones de los aprendices sobre la formación técnica y áreas transversales (habilidades blandas) en todo su proceso formativo.</p>';
$html .= '<p style="text-align:justify">Dicha encuesta se encuentra disponible en el <a href="https://www.desarrolloslope.com/encuestas" target="_blank">sistema de información de encuestas</a> donde cada aprendiz deberá iniciar sesión y proceder a dar solución a dicha encuesta. Si el aprendiz no conoce sus credenciales de acceso, puede acceder a <a href="https://desarrolloslope.com/encuestas/?contenido=src/identidad.php" target="_blank">este link</a> para verificar su identidad.</p>';
$html .= '<p style="text-align:justify">Le sugerimos imparta esta información a sus aprendices para que el proceso se realice de forma rápida y efectiva, ya que esta tarea es un requisito para finalizar su etapa lectiva y a la fecha no se ha recibido respuesta por parte de diversos aprendices.</p>';
$html .= '<p style="text-align:justify">Si se presenta el caso de que en alguna ficha uno o varios aprendices hayan decertado, se solicita comedidamente actualizar la información en la  plataforma <a href="http://oferta.senasofiaplus.edu.co/sofia-oferta/">Sofia Plus</a>. También se recomienda solicitar a los aprendices tener actualizados los datos personales y de contacto en la plataforma de Sofia Plus.</p>';
$html .= '<p style="text-align:justify">Al final de este correo va adjunta una hoja de cálculo donde están listadas todas las fichas (programas de formación) que todavía no han dado solución a la encuesta.</p>';
$html .= '<p style="text-align:justify">Si tiene alguna duda sobre este tema comuníquese al correo misionallope@misena.edu.co</p>';
$html .= '<br><p>Agradecemos su atención.</p><br/><br/>';
    $mail->setMensaje($html,true);
    $mail->addCopia('luna.juandavid95@gmail.com','BCC');
    $mail->addArchivo($filename,'asignacion_encuesta_por_fichas.xlsx');
    $mail->enviar();
    unlink($filename);
}