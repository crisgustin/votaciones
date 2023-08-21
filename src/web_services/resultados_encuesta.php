<?php
require_once dirname(__FILE__) . './../clases/Conector.php';
require_once dirname(__FILE__) . './../clases/Filter.php';
require_once dirname(__FILE__) . './../clases/NivelFormacion.php';
require_once dirname(__FILE__) . './../clases/Programa.php';
require_once dirname(__FILE__) . './../clases/Pregunta.php';
require_once dirname(__FILE__) . './../clases/FichaPrograma.php';
require_once dirname(__FILE__) . './../clases/Coordinacion.php';
require_once dirname(__FILE__) . './../clases/RespuestaPregunta.php';
$array = [];$mensaje = 'Mostrando resultados de todos los programas y fichas en general';

if (isset($_POST['id_encuesta'])) {
    $filtro = '';
    unset($_POST['tipo_grafica']);

    $oferta = $modalidad = $coord = $nivel = $fecha_inicial = $fecha_final = '';

    foreach ($_POST as $key => $value) {
        $var = $key;
        if($value!='') {
            if($key=='coord'||$key=='nivel'||$key=='oferta'||$key=='modalidad') {
                if ($key=='coord') $key = "fp.coordinacion";
                if ($key=='nivel') $key = "p.$key";
                $value = "'$value'";
            } elseif ($key=='id_encuesta') $key = "eu.$key";
            elseif ($key=='id_programa') $key= "fp.$key";
            elseif ($key=='id_ficha') $key = "fp.id";
            if($key!='fecha_inicial'&&$key!='fecha_final') $filtro .= " and $key = $value";
            $$var = $value;
        }
    }
    if(isset($_POST['oferta'])) $oferta = $_POST['oferta']=='A'? ' Oferta Abierta ':' Oferta Cerrada ';
    if(isset($_POST['modalidad'])) $modalidad=$_POST['modalidad']=='P'? ' Modalidad Presencial ':' Modalidad Virtual ';
    if (isset($_POST['id_ficha'])) {
        $ficha = new FichaPrograma('id', $_POST['id_ficha']);
        $mensaje = 'Mostrando resultados de la ficha '.$ficha->getNombre().' - (Coord. '.$ficha->getCoordinacion()->getNombre().' - Oferta '.$ficha->getNombreOferta().' - Modalidad '.$ficha->getNombreModalidad().')';
    } elseif (isset($_POST['coord'])) {
        $coord = new Coordinacion('id', $_POST['coord']);
        $mensaje = 'Mostrando resultados de';
        if (isset($_POST['id_programa']) || isset($_POST['nivel'])) {
            if (isset($_POST['id_programa'])) {
                $programa = new Programa('id', $_POST['id_programa']);
                $mensaje .= 'l programa ' . $programa->getNombre_completo() . ' - Coord. ' . $coord->getNombre();
            } else {
                $nivel = new NivelFormacion('id',$_POST['nivel']);
                $mensaje = 'Mostrando resultados de los programas de nivel '.$nivel->getNombre().'de la Coord. ' . $coord->getNombre();
            }
        } else $mensaje .= ' los programas de la Coord. ' . $coord->getNombre();
    } $mensaje .= $modalidad.$oferta;

    if ($fecha_inicial!=''&&$fecha_final!='') {
        if($fecha_inicial!=$fecha_final)$mensaje .= " desde el $fecha_inicial hasta el $fecha_final";
        else $mensaje .= " del día $fecha_inicial";
        $filtro .= " and eu.fecha_presentacion between '$fecha_inicial 00:00:00' and '$fecha_final 23:59:59'";
    }

    $sql = "select count(ru.id) as cantidad from respuesta_usuario as ru, pregunta as q, encuesta_usuario as eu, ficha_usuario as fu, ficha_programa as fp, programa as p where ru.id_pregunta = q.id and ru.id_encuesta = eu.id and eu.id_ficha = fu.id and fu.id_ficha = fp.id and fp.id_programa = p.id $filtro group by eu.id_encuesta;";
    $count = Conector::ejecutarQuery($sql);
    $count = count($count)>0?$count[0]['cantidad']:0;
    if($count>0) {
        $preguntas = Pregunta::getListaEnObjetos("id_encuesta = {$_POST['id_encuesta']}");
        $questions = []; $questions['escritas'] = [];
        $questions['seleccionadas'] = [];
        foreach ($preguntas as $pregunta) {
            $answers = [];
            if($pregunta->getTipo()!='RA') {
                $sql = "select rp.texto, rp.imagen, count(ru.id) as cantidad from respuesta_pregunta as rp, respuesta_usuario as ru, encuesta_usuario as eu, ficha_usuario as fu, ficha_programa as fp, programa as p where rp.id = ru.id_respuesta and ru.id_pregunta = {$pregunta->getId()} and ru.id_encuesta = eu.id and eu.id_ficha = fu.id and fu.id_ficha = fp.id and fp.id_programa = p.id $filtro group by rp.id;";
                $data = Conector::ejecutarQuery($sql);
                foreach ($data as $respuesta) {
                    $texto = $respuesta['texto'];
                    $img = $respuesta['imagen'];
                    if($texto==='Totalmente satisfecho') $color = '00ff00'; //verde
                    elseif($texto==='Parcialmente satisfecho') $color = 'ffff00'; //amarillo
                    elseif($texto==='Insatisfecho') $color = 'ff0000'; //rojo
                    else $color = 'e5e5e5'; //gris
                    if($img!=null) $texto = '<p class="text-bold">'.$texto.'</p><img src="'.$img.'" alt="'.$texto.'" style="width:auto;heigth:40px"/>';
                    $answer = ['respuesta' => $texto, 'cantidad' => $respuesta['cantidad'], 'color' => $color];
                    array_push($answers,$answer);
                }
            } else {
                $sql = "select ru.texto, concat(fp.ficha,' - ',nombre_nivel(pg.nivel),' en ',pg.nombre) as ficha from respuesta_usuario as ru, encuesta_usuario as eu, ficha_usuario as fu, ficha_programa as fp, programa as pg where ru.id_pregunta = {$pregunta->getId()} and ru.texto is not null and ru.id_respuesta is null and ru.id_encuesta = eu.id and eu.id_ficha = fu.id and fu.id_ficha = fp.id and fp.id_programa = pg.id $filtro order by fp.ficha asc;";
                $data = Conector::ejecutarQuery($sql);
                $ficha = '';
                foreach ($data as $respuesta) {
                    if($ficha!=$respuesta['ficha']) {
                        $ficha = $respuesta['ficha'];
                        $answers[$ficha]=[];
                    }
                    array_push($answers[$ficha],$respuesta['texto']);
                }
            }
            $question = ['id' => $pregunta->getId(), 'enunciado' => $pregunta->getEnunciado(), 'respuestas' => $answers];
            array_push($questions[$pregunta->getTipo()=='RA'?'escritas':'seleccionadas'],$question);
        } $array['preguntas'] = $questions;
    } else $mensaje = 'No se encontró ningún resultado';
    $sql = "select count(id) as cantidad from encuesta_usuario where id_encuesta = {$_POST['id_encuesta']};";
    $array['total_asignadas'] = Conector::ejecutarQuery($sql)[0]['cantidad'];

    $sql = "select count(id) as cantidad from encuesta_usuario where estado = 'P' and id_encuesta = {$_POST['id_encuesta']};";
    $array['total_faltantes'] = Conector::ejecutarQuery($sql)[0]['cantidad'];

    $sql = "select count(eu.id) as cantidad from encuesta_usuario as eu, ficha_usuario as fu, ficha_programa as fp, programa as p where eu.estado = 'F' and eu.id_ficha = fu.id and fu.id_ficha = fp.id and fp.id_programa = p.id $filtro;";
    $array['total_resueltas'] = Conector::ejecutarQuery($sql)[0]['cantidad'];
} else $mensaje = 'Error al cargar los datos';
$array['mensaje'] = $mensaje;
echo json_encode($array);
