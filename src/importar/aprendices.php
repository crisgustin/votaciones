<?php
date_default_timezone_set('America/Bogota');
use PhpOffice\PhpSpreadsheet\IOFactory;
require_once 'vendor/autoload.php';
require_once 'src/clases/NivelFormacion.php';
require_once 'src/clases/TipoDocumento.php';
require_once 'src/clases/Encuesta.php';
require_once 'src/clases/Programa.php';
require_once 'src/clases/FichaPrograma.php';
require_once 'src/clases/FichaUsuario.php';
require_once 'src/clases/EncuestaUsuario.php';
require_once 'src/clases/Filter.php';

if(isset($_FILES['archivo']) && file_exists($_FILES['archivo']['tmp_name'])&&isset($_REQUEST['id'])) {
    $header = ['Tipo de Documento','Número de Documento','Nombre','Apellidos','Celular','Correo Electrónico','Estado'];
    $extension = ucfirst(pathinfo($_FILES['archivo']['name'],PATHINFO_EXTENSION));
    $reader = IOFactory::createReader($extension);
    $reader->setLoadAllSheets();
    $spreadsheet = $reader->load($_FILES['archivo']['tmp_name']);
    $sheet = $spreadsheet->getActiveSheet();
    $max = $sheet->getHighestRowAndColumn();
    $data = $sheet->rangeToArray('A1:G'.$max['row']);
    $ficha_ok=$data[1][0]=='Ficha de Caracterización:'&&$data[1][2]!=null;
    $estado_ok=$data[2][0]=='Estado:'&&$data[2][2]!=null;
    $fecha_ok=$data[3][0]=='Fecha del Reporte:'&&$data[3][2]!=null;
    $gestor_ok=$data[4][0]=='GESTOR'&&$data[4][2]!=null;
    $correo_ok=$data[5][0]=='CORREO'&&$data[5][2]!=null;
    if($ficha_ok&&$estado_ok&&$fecha_ok&&$gestor_ok&&$correo_ok&&$header==$data[6]) {
        $ficha = trim(explode('-',$data[1][2])[0]);
        $fp = new FichaPrograma('id', $_REQUEST['id']);
        if(FichaPrograma::exists($ficha)&&$fp->getFicha()==$ficha) {
            $estado = $data[2][2];
            $fecha = $data[3][2];
            $gestor = mb_convert_case(Filter::drop_charset(Filter::clear($data[4][2])),MB_CASE_TITLE);
            $correo = mb_convert_case(Filter::drop_charset(Filter::clear($data[5][2])),MB_CASE_LOWER);
            $html = '<table class="table table-bordered table-condensed table-hover">';
            $html .= '<thead>';
            $html .= '<tr><th colspan="7" class="text-center" style="font-size:30px;">' . $data[0][0] . '</th></tr>';
            $html .= '<tr><th colspan="2">' . $data[1][0] . '</th><th colspan="5">' . $data[1][2] . '</th></tr>';
            $html .= '<tr><th colspan="2">' . $data[2][0] . '</th><th colspan="5">' . $data[2][2] . '</th></tr>';
            $html .= '<tr><th colspan="2">' . $data[3][0] . '</th><th colspan="5">' . $data[3][2] . '</th></tr>';
            $html .= '<tr><th colspan="2">' . $data[4][0] . '</th><th colspan="5">' . $data[4][2] . '</th></tr>';
            $html .= '<tr><th colspan="2">' . $data[5][0] . '</th><th colspan="5">' . $data[5][2] . '</th></tr>';
            $html .= '<tr><th>' . $header[0] . '</th><th>' . $header[1] . '</th><th>' . $header[2] . '</th><th>' . $header[3] . '</th><th>' . $header[4] . '</th><th>' . $header[5] . '</th><th>' . $header[6] . '</th></tr>';
            $html .= '</thead><tbody>';
            $alert = '';
            $array = [];
            unset($data[0]);
            unset($data[1]);
            unset($data[2]);
            unset($data[3]);
            unset($data[4]);
            unset($data[5]);
            unset($data[6]);
            foreach ($data as $object) {
                $usuario = mb_convert_case(trim($object[0]).trim($object[1]),MB_CASE_UPPER);
                $class = FichaUsuario::exist($ficha, $usuario) ? ' class="danger"' : null;
                $row = [];
                $html .= '<tr' . $class . '>';
                $i = 0;
                foreach ($object as $item) {
                    $item=Filter::drop_charset(Filter::clear($item));
                    if ($item == ''&&array_search($item,$object)!=4) {
                        $alert = '<div class="alert alert-danger"><p>El archivo tiene espacios en blanco, por favor corrígelo y <a href="src/formulario_excel.php?tipo=aprendiz&id=' . $_REQUEST['id'] . '" onclick="cargar_contenido_modal(this.href)" data-toggle="modal" data-target="#modal" class="btn btn-link">vuelve a intentar</a></p><a href="?contenido=src/ficha_detalle.php&id=' . $_REQUEST['id'] . '" class="btn btn-link">Volver a la ficha</a></div>';
                        break;
                    } else if(array_search($item,$object)==5&&!filter_var($item,FILTER_VALIDATE_EMAIL)) {
                        $alert = '<div class="alert alert-danger"><p>El archivo tiene datos de correo electrónico no válidos, por favor corrígelo y <a href="src/formulario_excel.php?tipo=aprendiz&id=' . $_REQUEST['id'] . '" onclick="cargar_contenido_modal(this.href)" data-toggle="modal" data-target="#modal" class="btn btn-link">vuelve a intentar</a></p><a href="?contenido=src/ficha_detalle.php&id=' . $_REQUEST['id'] . '" class="btn btn-link">Volver a la ficha</a></div>';
                        break;
                    }
                    $html .= "<td>$item</td>";
                    if ($class==null) {
                        $campo = null;
                        switch ($i) {
                            case 0:
                                $item = strlen($item)>2?TipoDocumento::getSiglas($item):$item;
                                $campo = 'tipo_id';
                                break;
                            case 1:
                                $item = $row['tipo_id'].$item;
                                unset($row['tipo_id']);
                                $campo = 'usuario';
                                break;
                            case 2:
                                $item = mb_convert_case($item,MB_CASE_TITLE);
                                $campo = 'nombres';
                                break;
                            case 3:
                                $item = mb_convert_case($item,MB_CASE_TITLE);
                                $campo = 'apellidos';
                                break;
                            case 4:$campo = 'telefono';break;
                            case 5:
                                $item = mb_convert_case($item,MB_CASE_LOWER);
                                $campo = 'correo';
                                break;
                            case 6:
                                $item = $estado;
                                $campo = 'estado';
                                break;
                        }
                        $row[$campo] = $item;
                    }
                    $i++;
                };
                if((count($header)-1)==count($row)) array_push($array,$row);
                $html .= '</tr>';
                if($alert!='') break;
            }
            $html .= '</tbody></table>';
            if (count($array)>0) {
                $_SESSION['aprendices'] = serialize($array);
                $html = '<form method="post" action="?contenido=src/importar/aprendices.php&id='.$_REQUEST['id'].'">
<input type="hidden" name="correo" value="'.$correo.'">
<input type="hidden" name="gestor" value="'.$gestor.'">
<input type="hidden" name="accion" value="importar">
<div class="form-group">
    <label for="select">Asignar Encuesta</label><br>
    <select name="id_encuesta" class="form-control" id="select">'.Encuesta::getOptionsHTML(null).'</select>
</div>
<div class="btn-group-lg">
                    <a href="?contenido=src/ficha_detalle.php&id='.$_REQUEST['id'].'" class="btn btn-default"><span class="fa fa-close"></span> Cancelar</a>
                    <button type="submit" class="btn btn-success"><span class="fa fa-save"></span> Guardar</button>
                    </div></form><br/>'.$html;
            } else $html = '<div class="alert alert-warning">Ningún aprendiz por importar...</div><a href="?contenido=src/ficha_detalle.php&id='.$_REQUEST['id'].'" class="btn btn-link">Volver a la ficha</a>'.$html;
            echo $alert!=''?$alert:$html;
        } else echo '<div class="alert alert-danger">Los aprendices no pertenecen al programa <strong>'.$fp->getNombre().'<a href="?contenido=src/ficha_detalle.php&id='.$_REQUEST['id'].'" class="btn btn-link">Volver a la ficha</a></strong></div>';
    } else echo '<div class="alert alert-danger">
    <p>El archivo seleccionado no tiene las casillas requeridas</p>
        <table border="1" class="table table-bordered table-hover table-condensed">
        <thead>
            <tr><th colspan="7" class="text-center" style="font-size:30px">Reporte de Aprendices</th></tr>
            <tr><th colspan="2">Ficha de Caracterización:</th><td colspan="5"># FICHA - NOMBRE DEL PROGRAMA</td></tr>
            <tr><th colspan="2">Estado:</th><td colspan="5">ESTADO DE LA FICHA</td></tr>
            <tr><th colspan="2">Fecha del Reporte:</th><td colspan="5">dia/mes/año</td></tr>
            <tr><th colspan="2">GESTOR:</th><td colspan="5">NOMBRE DEL INSTRUCTOR ENCARGADO</td></tr>
            <tr><th colspan="2">CORREO:</th><td colspan="5">CORREO DEL INSTRUCTOR</td></tr>
            <tr><th>Tipo de Documento</th><th>Número de Documento</th><th>Nombre</th><th>Apellidos</th><th>Celular</th><th>Correo Electrónico</th><th>Estado</th></tr>
        </thead>
    </table>
    <p>Corrige el archivo y <a href="src/formulario_excel.php?tipo=aprendices&id='.$_REQUEST['id'].'" onclick="cargar_contenido_modal(this.href)" data-toggle="modal" data-target="#modal" class="btn btn-link">vuelve a intentar</a></p>
    <a href="?contenido=src/ficha_detalle.php?i&id='.$_REQUEST['id'].'" class="btn btn-link">Volver a la ficha</a>
    </div>';
} elseif (isset($_POST['accion'])&&$_POST['accion']=='importar'&&isset($_SESSION['aprendices'])) {
    $data = unserialize($_SESSION['aprendices']);
    $fp = new FichaPrograma('id',$_REQUEST['id']);
    $fp->setCorreo($_REQUEST['correo']);
    $fp->setGestor($_REQUEST['gestor']);
    $fp->modificar();
    $count_aprendices = $count_encuestas=0;
    foreach ($data as $object) {
        if(Usuario::exists($object['usuario'])) $usuario = new Usuario('usuario', "'{$object['usuario']}'");
        else {
            $usuario = new Usuario(null,null);
            $usuario->setUsuario($object['usuario']);
            $usuario->setNombres($object['nombres']);
            $usuario->setApellidos($object['apellidos']);
            $usuario->setTelefono($object['telefono']);
            $usuario->setCorreo($object['correo']);
            $usuario->setClave(Usuario::clave_aleatoria());
            $usuario->setId_rol(2);
            $usuario->grabar();
        }
        $fu = new FichaUsuario(null,null);
        $fu->setId_ficha($fp->getId());
        $fu->setId_usuario($usuario->getId());
        if($fu->grabar()=='') $count_aprendices++;
        if(isset($_POST['id_encuesta'])) {
            $eu = new EncuestaUsuario(null,null);
            $eu->setId_ficha($fu->getId());
            $eu->setEstado('P');
            $eu->setId_encuesta($_POST['id_encuesta']);
            $eu->setFecha_asignacion(date('Y-m-d H:i:s'));
            if($eu->grabar()=='') $count_encuestas++;
        }
    }
    $count_encuestas=$count_encuestas>0?' y se asignaron '.$count_encuestas.' encuestas ':' ';
    echo '<div class="alert alert-success">Se importaron '.$count_aprendices.' aprendices'.$count_encuestas.'con éxito</div><script type="text/javascript">setTimeout(()=>{window.location="?contenido=src/ficha_detalle.php&id='.$_REQUEST['id'].'";},3000)</script>';
} else header('Location: ?contenido=src/ficha_detalle.php&id='.$_REQUEST['id']);
