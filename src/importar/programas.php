<?php
date_default_timezone_set('America/Bogota');
use PhpOffice\PhpSpreadsheet\IOFactory;
require_once 'vendor/autoload.php';
require_once 'src/clases/NivelFormacion.php';
require_once 'src/clases/Programa.php';
require_once 'src/clases/FichaPrograma.php';
require_once 'src/clases/Coordinacion.php';
require_once 'src/clases/Filter.php';

if(isset($_FILES['archivo']) && file_exists($_FILES['archivo']['tmp_name'])) {

    $extension = ucfirst(pathinfo($_FILES['archivo']['name'],PATHINFO_EXTENSION));
    $reader = IOFactory::createReader($extension);
    $reader->setLoadAllSheets();
    $spreadsheet = $reader->load($_FILES['archivo']['tmp_name']);
    $data = $spreadsheet->getSheet(0)->toArray();
    $header_ok=true;$i=0;
    $header = ['NIVEL','NOMBRE PROGRAMA','NUMERO DE FICHA','COORDINACION ACADEMICA','FECHA DE INICIO','FECHA FIN','MODALIDAD','OFERTA'];
    foreach($data[0] as $item) {
        $item = Filter::drop_charset(Filter::clear($item));
        if($item!=$header[$i]) {$header_ok=false;break;}
        $i++;
    }
    if($header_ok) {
        unset($data[0]);
        $html = '<table class="table table-bordered table-condensed table-hover">';
        $html .= '<thead><tr class="active"><th>'.$header[0].'</th><th>'.$header[1].'</th><th>'.$header[2].'</th><th>'.$header[3].'</th><th>'.$header[4].'</th><th>'.$header[5].'</th><th>'.$header[6].'</th><th>'.$header[7].'</th></tr></thead>';
        $html .= '<tbody>';
        $alert='';
        $array=[];
        foreach ($data as $object) {
            $ficha= Filter::drop_charset(Filter::clear($object[2]));
            $class=FichaPrograma::exists($ficha)?' class="danger"':'';
            $row = [];
            $html .= '<tr'.$class.'>';
            $i=0;
            foreach ($object as $item) {
                $item = Filter::drop_charset(Filter::clear($item));
                if($item=='') {
                    $alert='<div class="alert alert-danger"><p>El archivo tiene espacios en blanco, por favor corrígelo y <a href="src/formulario_excel.php?tipo=programa" onclick="cargar_contenido_modal(this.href)" data-toggle="modal" data-target="#modal" class="btn btn-link">vuelve a intentar</a></p><a href="?contenido=src/programas.php" class="btn btn-link">Volver a la lista de programas</a></div>';
                    break;
                }
                $html .= '<td>'.$item.'</td>';
                if ($class=='') {
                    $campo = '';
                    switch ($i) {
                        case 0:
                            $item = NivelFormacion::getSiglas($item);
                            $campo = 'nivel';
                            break;
                        case 1:
                            $item = mb_convert_case($item, MB_CASE_TITLE);
                            $campo = 'programa';
                            break;
                        case 2:
                            $item = $ficha;
                            $campo = 'ficha';
                            break;
                        case 3:
                            $item = str_replace('Coord. ', '', mb_convert_case($item, MB_CASE_TITLE));
                            $item = Coordinacion::getSiglas($item);
                            $campo = 'coordinacion';
                            break;
                        case 4:
                            $campo = 'fecha_inicio';
                            $item = date('Y-m-d',strtotime($item));
                            break;
                        case 5:
                            $campo = 'fecha_fin';
                            $item = date('Y-m-d',strtotime($item));
                            break;
                        case 6:
                            $item = mb_convert_case($item, MB_CASE_LOWER);
                            $item = $item == 'presencial' ? 'P' : 'V';
                            $campo = 'modalidad';
                            break;
                        case 7:
                            $item = mb_convert_case($item, MB_CASE_LOWER);
                            $item = $item == 'abierta' ? 'A' : 'C';
                            $campo = 'oferta';
                            break;
                    }
                    $row[$campo]=$item;
                }
                $i++;
            }
            if(count($row)==count($header)) {
                $days=$row['nivel']=='AO'?90:180;
                $fin_lectiva = date('Y-m-d',strtotime($row['fecha_fin'].' -'.$days.' days'));
                $row['fin_lectiva']=$fin_lectiva;
                array_push($array,$row);
            }
            $html .= '</tr>';
            if($alert!='') break;
        }
        $html .= '</tbody>';
        $html .= '</table>';
        if (count($array)>0) {
            $_SESSION['fichas'] = serialize($array);
            $html = '<form method="post" action="?contenido=src/importar/programas.php"><input type="hidden" name="accion" value="importar"><div class="btn-group-lg">
                    <a href="?contenido=src/programas.php" class="btn btn-default"><span class="fa fa-close"></span> Cancelar</a>
                    <button type="submit" class="btn btn-success"><span class="fa fa-save"></span> Guardar</button>
                    </div></form><br/>'.$html;
        } else $html = '<div class="alert alert-warning">Ninguna ficha por importar...</div><a href="?contenido=src/programas.php" class="btn btn-link">Volver a la lista de programas</a>'.$html;
        echo $alert!=''?$alert:$html;
    } else echo '<div class="alert alert-danger">
    <p>El archivo seleccionado no tiene las casillas requeridas</p>
        <ul>
            <li>NIVEL</li>
            <li>NOMBRE PROGRAMA</li>
            <li>NUMERO DE FICHA</li>
            <li>COORDINACION ACADEMICA</li>
            <li>FECHA DE INICIO</li>
            <li>FECHA FIN</li>
            <li>MODALIDAD</li>
            <li>OFERTA</li>
        </ul> <p>Corrige el archivo y <a href="src/formulario_excel.php?tipo=programa" onclick="cargar_contenido_modal(this.href)" data-toggle="modal" data-target="#modal" class="btn btn-link">vuelve a intentar</a></p>
        <a href="?contenido=src/programas.php" class="btn btn-link">Volver a la lista de programas</a>
    </div>';
} elseif(isset($_POST['accion'])&&$_POST['accion']=='importar'&&isset($_SESSION['fichas'])) {
    $data = unserialize($_SESSION['fichas']);
    $count = 0;
    foreach ($data as $object) {
        $programa = Programa::getObjectByName($object['nivel'],$object['programa']);
        if($programa==null) {
            $programa = new Programa(null,null);
            $programa->setNivel($object['nivel']);
            $programa->setNombre($object['programa']);
            $programa->grabar();
        }
        $ficha = new FichaPrograma(null, null);
        $ficha->setFicha($object['ficha']);
        $ficha->setId_programa($programa->getId());
        $ficha->setFecha_inicio($object['fecha_inicio']);
        $ficha->setFin_lectiva($object['fin_lectiva']);
        $ficha->setFecha_fin($object['fecha_fin']);
        $ficha->setModalidad($object['modalidad']);
        $ficha->setOferta($object['oferta']);
        $ficha->setCoordinacion($object['coordinacion']);
        if($ficha->grabar()=='') $count++;
    }
    unset($_SESSION['fichas']);
    echo '<div class="alert alert-success">¡Se importaron '.$count.' fichas exitosamente!</div><script type="text/javascript">setTimeout(()=>window.location="?contenido=src/programas.php",3000)</script>';
} else header('Location: ?contenido=src/programas.php');
