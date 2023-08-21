<?php
require_once '../../clases/Conector.php';
require_once '../../clases/Encuesta.php';
require_once '../../clases/Programa.php';
require_once '../../clases/FichaPrograma.php';
require_once '../../clases/FichaUsuario.php';
require_once '../../clases/Usuario.php';
require_once '../../clases/NivelFormacion.php';
require_once '../../clases/Pregunta.php';
require_once '../../clases/CategoriaEncuesta.php';
require_once '../../clases/EncuestaUsuario.php';
require_once '../../clases/RespuestaUsuario.php';
require_once '../../clases/RespuestaPregunta.php';
require_once '../../helpers/EncuestaIndividual.php';
require '../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

$html = '';
if(isset($_GET['id_encuesta'])) {
    $resultado = new EncuestaIndividual($_GET['id_encuesta']);
    $reader = new Html();
    $html = $resultado->get_table_html(true);
    $reader->setSheetIndex(0);
    $spreadsheet = $reader->loadFromString($html);
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle($resultado->get_aprendiz()->getUsuario());
    $cols = ['A','B','C','D'];
    foreach($cols as $col) {
        //$sheet->getColumnDimension($col)->setAutoSize(true);
        $sheet->getColumnDimension($col)->setWidth(30);
    }
    $sheet->getColumnDimension('D')->setWidth(50);
    $i = 1;
    while($i<=$sheet->getHighestRow()) {
        $sheet->getRowDimension($i)->setRowHeight(30);
        $i++;
    }
    $sheet->getRowDimension(1)->setRowHeight(40);

    $row_header_count = 7;
    $style_sheet = ['font' => ['name' => 'Arial', 'size' => 10],'alignment' => ['wrapText' => true,'vertical' => Alignment::VERTICAL_CENTER], 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM,'color' => ['argb' => '000000']]]];
    $style_head = ['font' => ['bold' => true], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]];
    $sheet->getStyle("A1:D{$sheet->getHighestRow()}")->applyFromArray($style_sheet);
    $sheet->getStyle("A1:D$row_header_count")->applyFromArray($style_head);
    $font_title = $sheet->getStyle("A1")->getFont();
    $font_title->setSize(20);
    $font_subtitle = $sheet->getStyle("A2")->getFont();
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
    header("Content-Disposition: attachment;filename=cumplimiento_individual.xlsx");
    header("Content-Transfer-Encoding: binary ");
    ob_end_clean();
    ob_start();
    $writer->save('php://output');
    } else echo 'No se encontró ningún registro!<script type="text/javascript">setTimeout(()=>{window.history.back()},3000)</script>';