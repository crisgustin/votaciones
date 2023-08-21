<?php
require_once '../../clases/Conector.php';
require_once '../../clases/Encuesta.php';
require_once '../../clases/Programa.php';
require_once '../../clases/FichaPrograma.php';
require_once '../../clases/NivelFormacion.php';
require_once '../../helpers/Incumplimiento.php';
require '../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

$html = '';
if(isset($_GET['id_encuesta'])) {
    $id_programa = isset($_GET['id_programa'])?$_GET['id_programa']:null;
    $id_ficha = isset($_GET['id_ficha'])?$_GET['id_ficha']:null;
    $incumplimiento = new Incumplimiento($_GET['id_encuesta'],$id_programa,$id_ficha);
    $datos = $incumplimiento->get_array();
    if(count($datos)>0) {
        $reader = new Html();
        $spreadsheet = null;
        $i = 0;
        foreach($datos as $matriz) {
            $html = '<table>';
            $html .= $incumplimiento->get_header_html();
            $html .= '<tbody>';
            foreach ($matriz['usuarios'] as $objeto) {
                $html .= '<tr>';
                $html .= '<td>' . $objeto['usuario'] . '</td>';
                $html .= '<td>' . $objeto['nombres'] . '</td>';
                $html .= '<td>' . $objeto['apellidos'] . '</td>';
                $html .= '<td>' . $objeto['telefono'] . '</td>';
                $html .= '<td>' . $objeto['correo'] . '</td>';
                $html .= '<td>' . $objeto['ficha'] . '</td>';
                $html .= '</tr>';
            } $html .= '</tbody>';
            $html .= '</table>';
            $reader->setSheetIndex($i);
            $spreadsheet = $reader->loadFromString($html,$spreadsheet);
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle($matriz['modalidad']);
            $cols = ['A','B','C','D','E','F'];
            foreach($cols as $col) {$sheet->getColumnDimension($col)->setAutoSize(true);}
            $sheet->getRowDimension(1)->setRowHeight(40);
            $row_header_count = $id_ficha!=null?6:4;
            $style_sheet = ['font' => ['name' => 'Arial', 'size' => 10],'alignment' => ['wrapText' => true,'vertical' => Alignment::VERTICAL_CENTER], 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM,'color' => ['argb' => '000000']]]];
            $style_head = ['font' => ['bold' => true], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]];
            $sheet->getStyle("A1:F{$sheet->getHighestRow()}")->applyFromArray($style_sheet);
            $sheet->getStyle("A1:F$row_header_count")->applyFromArray($style_head);
            $font_title = $sheet->getStyle("A1")->getFont();
            $font_title->setSize(20);
            $font_subtitle = $sheet->getStyle("A2")->getFont();
            $i++;
        }
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
        header("Content-Disposition: attachment;filename=reporte_incumplimiento.xlsx");
        header("Content-Transfer-Encoding: binary ");
        ob_end_clean();
        ob_start();
        $writer->save('php://output');
    } else echo 'No se encontró ningún registro!<script type="text/javascript">setTimeout(()=>{window.history.back()},3000)</script>';
}