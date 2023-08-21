<?php
date_default_timezone_set('America/Bogota');
require_once '../../clases/Conector.php';
require_once '../../clases/Encuesta.php';
require '../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

if(isset($_GET['id_encuesta'])&&isset($_GET['rango'])) {
    $dias = ['1' => 'Lunes','2' => 'Martes','3' => 'Miércoles','4' => 'Jueves','5' => 'Viernes','6' => 'Sábado','7' => 'Domingo'];
    $meses = ['1' => 'Enero', '2' => 'Febrero', '3' => 'Marzo', '4' => 'Abril', '5' => 'Mayo', '6' => 'Junio', '7' => 'Julio', '8' => 'Agosto', '9' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'];
    $encuesta = new Encuesta('id', $_GET['id_encuesta']);
    if($_GET['rango']=='semana') {
        $fecha1 = date('Y-m-d',strtotime(date('Y-m-d').'- 7 days'));
        $fecha2 = date('Y-m-d');
    } elseif ($_GET['rango']=='mes') {
        $fecha1 = date('Y-m').'-01';
        $fecha2 = date('Y-m-t');
    }

    $rango = "'$fecha1 00:00:00' and '$fecha2 23:59:59'";
    $sql = "select date(fecha_presentacion) as fecha, count(id) as cantidad from encuesta_usuario where estado = 'F' and fecha_presentacion between $rango group by date(fecha_presentacion);";
    $datos = Conector::ejecutarQuery($sql);

    $html = '<table>';
    $html .= '<thead>
<tr><th colspan="2">Reporte de cumplimiento</th></tr>
<tr><th colspan="2">'.$encuesta->getNombre().'</th></tr>
<tr><th colspan="2">'.$fecha1.' hasta '.$fecha2.'</th></tr>
<tr><th colspan="2"></th></tr>
<tr><th>Fecha</th><th>Cantidad</th></tr>
</thead><tbody>';
    $suma = 0;
    foreach ($datos as $obj) {
        $time = strtotime($obj['fecha']);
        $dia = $dias[date('N',$time)];
        $mes = date('n',$time);
        $mes = $meses[$mes];
        $html .= '<tr><td>'.$dia.' '.date('d',$time).' de '.$mes.'</td><td>'.$obj['cantidad'].'</td></tr>';
        $suma += $obj['cantidad'];
    }
    $html .= '<tr><td>TOTAL</td><td>'.$suma.'</td></tr></tbody></table>';
    $reader = new Html();
    $spreadsheet = $reader->loadFromString($html);
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Hoja 1');
    $sheet->getColumnDimension('A')->setWidth(50);
    $sheet->getColumnDimension('B')->setWidth(50);
    $sheet->getRowDimension(1)->setRowHeight(40);
    $sheet->getRowDimension($sheet->getHighestRow())->setRowHeight(30);
    $style_sheet = ['font' => ['name' => 'Arial', 'size' => 10],'alignment' => ['wrapText' => true,'vertical' => Alignment::VERTICAL_CENTER], 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM,'color' => ['argb' => '000000']]]];
    $style_head = ['font' => ['bold' => true], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]];
    $sheet->getStyle("A1:B{$sheet->getHighestRow()}")->applyFromArray($style_sheet);
    $sheet->getStyle("A1:B5")->applyFromArray($style_head);
    $sheet->getStyle("A{$sheet->getHighestRow()}:B{$sheet->getHighestRow()}")->applyFromArray($style_head);
    $font_title = $sheet->getStyle("A1")->getFont();
    $font_title->setSize(20);
    $properties = $spreadsheet->getProperties();
    $properties->setCreator('SI de Encuestas');
    $properties->setCompany('SENA Regional Nariño');
    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
    header("Content-Disposition: attachment;filename=cumplimiento_encuesta.xlsx");
    header("Content-Transfer-Encoding: binary ");
    ob_end_clean();
    ob_start();
    $writer->save('php://output');
}
