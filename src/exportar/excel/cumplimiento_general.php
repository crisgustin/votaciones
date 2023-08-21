<?php
require_once '../../clases/Conector.php';
require_once '../../clases/Encuesta.php';
require '../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

if(isset($_GET['id_encuesta'])) {
    $encuesta = new Encuesta('id', $_GET['id_encuesta']);
    $meses = ['1' => 'Enero', '2' => 'Febrero', '3' => 'Marzo', '4' => 'Abril', '5' => 'Mayo', '6' => 'Junio', '7' => 'Julio', '8' => 'Agosto', '9' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'];
    $sql = "select month(fecha_asignacion) as mes, count(id) as cantidad from encuesta_usuario group by month(fecha_asignacion);";
    $asignaciones = Conector::ejecutarQuery($sql);

    $sql = "select month(fecha_asignacion) as mes, count(id) as cantidad from encuesta_usuario where estado = 'F' and fecha_presentacion is not null group by month(fecha_asignacion);";
    $cumplimiento = Conector::ejecutarQuery($sql);
    $html = '<table>';
    $html .= '<thead>
<tr><th colspan="4">Reporte de cumplimiento</th></tr>
<tr><th colspan="4">'.$encuesta->getNombre().'</th></tr>
<tr><th colspan="4">'.$encuesta->getObjetivo().'</th></tr>
<tr><th colspan="4"></th></tr>
<tr><th>Mes</th><th>Asignaciones</th><th>Cumplimiento</th><th>Faltantes</th></tr>
</thead><tbody>';
    $i =0;
    foreach ($asignaciones as $asignacion) {
        $mes = $meses[$asignacion['mes']];
        $faltantes = $asignacion['cantidad'] - $cumplimiento[$i]['cantidad'];
        $html .= '<tr>';
        $html .= '<td>'.$mes.'</td>';
        $html .= '<td>'.$asignacion['cantidad'].'</td>';
        $html .= '<td>'.$cumplimiento[$i]['cantidad'].'</td>';
        $html .= '<td>'.$faltantes.'</td>';
        $html .= '</tr>';
        $i++;
    }
    $html .= '<tr>';
    $html .= '<td>TOTAL</td>';
    $html .= '<td>=SUMA(B6:B'.(count($asignaciones)+5).')</td>';
    $html .= '<td>=SUMA(C6:C'.(count($cumplimiento)+5).')</td>';
    $html .= '<td>=SUMA(D6:D'.(count($asignaciones)+5).')</td>';
    $html .= '</tr>';
    $html .= '</tbody></table>';
    $reader = new Html();
    $spreadsheet = $reader->loadFromString($html);
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Hoja 1');
    $cols = ['A','B','C','D'];
    foreach($cols as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    $sheet->getRowDimension(1)->setRowHeight(40);
    $sheet->getRowDimension(2)->setRowHeight(40);
    $sheet->getRowDimension(3)->setRowHeight(60);
    $sheet->getRowDimension(4)->setRowHeight(30);
    $sheet->getRowDimension($sheet->getHighestRow())->setRowHeight(30);
    $style_sheet = ['font' => ['name' => 'Arial', 'size' => 10],'alignment' => ['wrapText' => true,'vertical' => Alignment::VERTICAL_CENTER], 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM,'color' => ['argb' => '000000']]]];
    $style_head = ['font' => ['bold' => true], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]];
    $sheet->getStyle("A1:D{$sheet->getHighestRow()}")->applyFromArray($style_sheet);
    $sheet->getStyle("A1:D5")->applyFromArray($style_head);
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
    header("Content-Disposition: attachment;filename=reporte_general_encuesta.xlsx");
    header("Content-Transfer-Encoding: binary ");
    ob_end_clean();
    ob_start();
    $writer->save('php://output');
}
